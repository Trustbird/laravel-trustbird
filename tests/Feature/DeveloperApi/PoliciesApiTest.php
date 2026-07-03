<?php

declare(strict_types=1);

namespace Trustbird\Tests\Feature\DeveloperApi;

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;
use Trustbird\Policies\Enums\PolicyVersionStatus;
use Trustbird\Policies\Events\PolicyReviewed;
use Trustbird\Policies\Events\PolicyVersionDrafted;
use Trustbird\Policies\Events\PolicyVersionPublished;
use Trustbird\Policies\Events\PolicyVersionUpdated;
use Trustbird\Policies\Models\Policy;
use Trustbird\Policies\Models\PolicyVersion;

beforeEach(fn () => Event::fake());

it('can create a policy via the facade', function () {
    $policy = Trustbird::policies()->create(
        title: 'InfoSec Policy',
        content: 'Content here'
    );

    expect($policy)->toBeInstanceOf(Policy::class)
        ->and($policy->title)->toBe('InfoSec Policy');

    expect($policy->versions)->toHaveCount(1);

    Event::assertDispatched('eloquent.created: '.Policy::class);
    Event::assertDispatched(PolicyVersionDrafted::class);
});

it('can update a policy via the facade', function () {
    $policy = Policy::factory()->create(['title' => 'Old Title']);

    $updated = Trustbird::policies()->update(
        $policy,
        title: 'New Title'
    );

    expect($updated->title)->toBe('New Title');
    Event::assertDispatched('eloquent.updated: '.Policy::class);
});

it('can draft a version via the facade', function () {
    $policy = Policy::factory()->create();

    $version = Trustbird::policies()->draftVersion(
        policy: $policy,
        content: 'New content'
    );

    expect($version)->toBeInstanceOf(PolicyVersion::class)
        ->and($version->content)->toBe('New content')
        ->and($version->status)->toBe(PolicyVersionStatus::Draft);

    Event::assertDispatched(PolicyVersionDrafted::class);
});

it('can update a version via the facade', function () {
    $policy = Policy::factory()->withDraftVersion()->create();
    $version = $policy->versions->first();

    $updated = Trustbird::policies()->updateVersion(
        version: $version,
        content: 'Updated content'
    );

    expect($updated->content)->toBe('Updated content');
    Event::assertDispatched(PolicyVersionUpdated::class);
});

it('can publish a version via the facade', function () {
    $policy = Policy::factory()->withDraftVersion()->create();
    $version = $policy->versions->first();

    $published = Trustbird::policies()->publishVersion(
        policy: $policy,
        version: $version
    );

    expect($published->status)->toBe(PolicyVersionStatus::Published);
    Event::assertDispatched(PolicyVersionPublished::class);
});

it('can review a policy via the facade', function () {
    $policy = Policy::factory()->create();

    $reviewed = Trustbird::policies()->review(
        policy: $policy,
        notes: 'Looks good'
    );

    expect($reviewed->reviewed_at)->not->toBeNull();
    Event::assertDispatched(PolicyReviewed::class);
});
