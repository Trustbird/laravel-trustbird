<?php

declare(strict_types=1);

use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;
use Trustbird\Policies\Enums\PolicyVersionStatus;
use Trustbird\Policies\Events\PolicyReviewed;
use Trustbird\Policies\Events\PolicyVersionDrafted;
use Trustbird\Policies\Events\PolicyVersionPublished;
use Trustbird\Policies\Events\PolicyVersionUpdated;
use Trustbird\Policies\Models\Policy;
use Trustbird\Policies\Models\PolicyVersion;

test('it can create a policy with an initial draft version', function () {
    $owner = Person::factory()->create();
    $reviewer = Person::factory()->create(['workspace_id' => $owner->workspace_id]);

    $policy = Trustbird::policies()->create(
        title: 'Information Security Policy',
        content: 'All employees must protect company data.',
        ownerId: $owner->id,
        reviewerId: $reviewer->id,
        changeSummary: 'Initial draft',
    );

    expect($policy)->toBeInstanceOf(Policy::class)
        ->title->toBe('Information Security Policy')
        ->owner_id->toBe($owner->id)
        ->reviewer_id->toBe($reviewer->id)
        ->hasPublishedVersion()->toBeFalse();

    expect($policy->versions)->toHaveCount(1);
    expect($policy->versions->first())
        ->version_number->toBe(1)
        ->status->toBe(PolicyVersionStatus::Draft)
        ->content->toBe('All employees must protect company data.');
});

test('it can update a policy', function () {
    $policy = Policy::factory()->withDraftVersion()->create(['title' => 'Old title']);

    $updatedPolicy = Trustbird::policies()->update(
        policy: $policy,
        title: 'Updated title',
    );

    expect($updatedPolicy->title)->toBe('Updated title');
});

test('it can draft a new policy version and dispatches event', function () {
    Event::fake();

    $policy = Policy::factory()->withPublishedVersion()->create();

    $version = Trustbird::policies()->draftVersion(
        policy: $policy,
        content: 'Updated policy content.',
        notes: 'Annual review updates',
    );

    expect($version)->toBeInstanceOf(PolicyVersion::class)
        ->version_number->toBe(2)
        ->status->toBe(PolicyVersionStatus::Draft)
        ->content->toBe('Updated policy content.');

    Event::assertDispatched(PolicyVersionDrafted::class, function ($event) use ($policy, $version) {
        return $event->policy->id === $policy->id
            && $event->version->id === $version->id;
    });
});

test('it can update a draft policy version and dispatches event', function () {
    Event::fake();

    $policy = Policy::factory()->withDraftVersion()->create();
    $version = $policy->versions->first();

    $updatedVersion = Trustbird::policies()->updateVersion(
        version: $version,
        content: 'Revised draft content.',
    );

    expect($updatedVersion->content)->toBe('Revised draft content.');

    Event::assertDispatched(PolicyVersionUpdated::class, function ($event) use ($version) {
        return $event->version->id === $version->id;
    });
});

test('it cannot update a published policy version', function () {
    $policy = Policy::factory()->withPublishedVersion()->create();
    $version = $policy->publishedVersion;

    Trustbird::policies()->updateVersion(
        version: $version,
        content: 'Should not work.',
    );
})->throws(InvalidArgumentException::class, 'Only draft policy versions can be updated.');

test('it can explicitly publish a draft version and dispatches event', function () {
    Event::fake();

    $policy = Policy::factory()->withDraftVersion()->create();
    $version = $policy->versions->first();
    $publisher = Person::factory()->create(['workspace_id' => $policy->workspace_id]);

    $publishedVersion = Trustbird::policies()->publishVersion(
        policy: $policy,
        version: $version,
        publishedById: $publisher->id,
    );

    $policy->refresh();

    expect($publishedVersion->status)->toBe(PolicyVersionStatus::Published)
        ->and($publishedVersion->published_by_id)->toBe($publisher->id)
        ->and($publishedVersion->published_at)->not->toBeNull()
        ->and($policy->current_version_id)->toBe($version->id)
        ->and($policy->hasPublishedVersion())->toBeTrue();

    Event::assertDispatched(PolicyVersionPublished::class, function ($event) use ($policy, $version) {
        return $event->policy->id === $policy->id
            && $event->version->id === $version->id;
    });
});

test('it supersedes the previous published version when publishing a new one', function () {
    $policy = Policy::factory()->withPublishedVersion()->create();
    $previousVersion = $policy->publishedVersion;

    $draft = Trustbird::policies()->draftVersion(
        policy: $policy,
        content: 'Version 2 content.',
    );

    Trustbird::policies()->publishVersion(
        policy: $policy,
        version: $draft,
    );

    expect($previousVersion->fresh()->status)->toBe(PolicyVersionStatus::Superseded)
        ->and($draft->fresh()->status)->toBe(PolicyVersionStatus::Published)
        ->and($policy->fresh()->current_version_id)->toBe($draft->id);
});

test('it cannot publish a version that does not belong to the policy', function () {
    $policy = Policy::factory()->withDraftVersion()->create();
    $otherVersion = PolicyVersion::factory()->draft()->create();

    Trustbird::policies()->publishVersion(
        policy: $policy,
        version: $otherVersion,
    );
})->throws(InvalidArgumentException::class, 'The policy version does not belong to this policy.');

test('it cannot publish a non-draft version', function () {
    $policy = Policy::factory()->withPublishedVersion()->create();

    Trustbird::policies()->publishVersion(
        policy: $policy,
        version: $policy->publishedVersion,
    );
})->throws(InvalidArgumentException::class, 'Only draft policy versions can be published.');

test('it can review a policy and dispatches event', function () {
    Event::fake();

    $policy = Policy::factory()->withPublishedVersion()->create();
    $reviewer = Person::factory()->create(['workspace_id' => $policy->workspace_id]);
    $reviewedAt = now()->subHour();
    $nextReviewAt = now()->addYear();

    $reviewedPolicy = Trustbird::policies()->review(
        policy: $policy,
        reviewedAt: $reviewedAt,
        nextReviewAt: $nextReviewAt,
        reviewerId: $reviewer->id,
    );

    expect($reviewedPolicy->reviewer_id)->toBe($reviewer->id)
        ->and($reviewedPolicy->reviewed_at->toDateTimeString())->toBe($reviewedAt->toDateTimeString())
        ->and($reviewedPolicy->next_review_at->toDateTimeString())->toBe($nextReviewAt->toDateTimeString());

    Event::assertDispatched(PolicyReviewed::class, function ($event) use ($policy) {
        return $event->policy->id === $policy->id;
    });
});

test('it sets reviewed_at automatically when reviewing a policy', function () {
    $policy = Policy::factory()->create();

    $reviewedPolicy = Trustbird::policies()->review(policy: $policy);

    expect($reviewedPolicy->reviewed_at)->not->toBeNull();
});

test('it can determine policy review state', function () {
    $unscheduledPolicy = Policy::factory()->create();
    $duePolicy = Policy::factory()->dueForReview()->create();
    $scheduledPolicy = Policy::factory()->create(['next_review_at' => now()->addMonth()]);

    expect($unscheduledPolicy->needsReview())->toBeTrue();
    expect($duePolicy->needsReview())->toBeTrue();
    expect($scheduledPolicy->needsReview())->toBeFalse();
});

test('it covers all policy and version model methods', function () {
    $owner = Person::factory()->create();
    $reviewer = Person::factory()->create(['workspace_id' => $owner->workspace_id]);
    $publisher = Person::factory()->create(['workspace_id' => $owner->workspace_id]);

    $policy = Policy::factory()->withPublishedVersion()->create([
        'owner_id' => $owner->id,
        'reviewer_id' => $reviewer->id,
        'metadata' => ['category' => 'security'],
        'reviewed_at' => now(),
        'next_review_at' => now()->addYear(),
    ]);

    $publishedVersion = $policy->publishedVersion;
    $publishedVersion->update(['published_by_id' => $publisher->id]);

    $draftVersion = PolicyVersion::factory()->draft()->create([
        'workspace_id' => $policy->workspace_id,
        'policy_id' => $policy->id,
        'version_number' => 2,
    ]);

    $supersededVersion = PolicyVersion::factory()->superseded()->create([
        'workspace_id' => $policy->workspace_id,
        'policy_id' => $policy->id,
        'version_number' => 3,
    ]);

    expect($policy->owner->id)->toBe($owner->id)
        ->and($policy->reviewer->id)->toBe($reviewer->id)
        ->and($policy->publishedVersion->id)->toBe($publishedVersion->id)
        ->and($policy->versions)->toHaveCount(3)
        ->and($policy->hasPublishedVersion())->toBeTrue()
        ->and($policy->metadata['category'])->toBe('security');

    expect($publishedVersion->isPublished())->toBeTrue()
        ->and($publishedVersion->isDraft())->toBeFalse()
        ->and($publishedVersion->isSuperseded())->toBeFalse()
        ->and($publishedVersion->canBeEdited())->toBeFalse()
        ->and($publishedVersion->canBePublished())->toBeFalse()
        ->and($publishedVersion->publishedBy->id)->toBe($publisher->id)
        ->and($publishedVersion->policy->id)->toBe($policy->id);

    expect($draftVersion->isDraft())->toBeTrue()
        ->and($draftVersion->canBeEdited())->toBeTrue()
        ->and($draftVersion->canBePublished())->toBeTrue();

    expect($supersededVersion->isSuperseded())->toBeTrue();

    expect(Policy::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Policy\PolicyFactory::class);
    expect(PolicyVersion::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Policy\PolicyVersionFactory::class);
});
