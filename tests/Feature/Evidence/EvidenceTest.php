<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Controls\Models\Control;
use Trustbird\Evidence\Enums\EvidenceStatus;
use Trustbird\Evidence\Enums\EvidenceType;
use Trustbird\Evidence\Events\EvidenceReviewed;
use Trustbird\Evidence\Models\Evidence;
use Trustbird\Evidence\Models\EvidenceRelation;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;

beforeEach(fn () => Event::fake());

test('it can register evidence and dispatches eloquent event', function (): void {
    $owner = Person::factory()->create();

    $evidence = Trustbird::evidence()->create(
        title: 'Penetration test report',
        type: EvidenceType::Document,
        status: EvidenceStatus::Draft,
        ownerId: $owner->id,
        metadata: ['sensitivity' => 'confidential'],
    );

    expect($evidence)->toBeInstanceOf(Evidence::class)
        ->and($evidence->title)->toBe('Penetration test report')
        ->and($evidence->type)->toBe(EvidenceType::Document)
        ->and($evidence->metadata)->toBe(['sensitivity' => 'confidential']);

    Event::assertDispatched('eloquent.created: '.Evidence::class);
});

test('it can review evidence and dispatches semantic event', function (): void {
    $evidence = Evidence::factory()->create(['status' => EvidenceStatus::UnderReview]);
    $reviewer = Person::factory()->create(['workspace_id' => $evidence->workspace_id]);

    $reviewed = Trustbird::evidence()->review(
        evidence: $evidence,
        reviewerId: $reviewer->id,
        status: EvidenceStatus::Active,
        nextReviewAt: now()->addMonths(6),
    );

    expect($reviewed->reviewer_id)->toBe($reviewer->id)
        ->and($reviewed->status)->toBe(EvidenceStatus::Active)
        ->and($reviewed->reviewed_at)->not->toBeNull();

    Event::assertDispatched(EvidenceReviewed::class);
});

test('it can relate evidence to another Trustbird object', function (): void {
    $evidence = Evidence::factory()->create();
    $control = Control::factory()->create(['workspace_id' => $evidence->workspace_id]);

    $relation = Trustbird::evidence()->relate(
        evidence: $evidence,
        related: $control,
        metadata: ['purpose' => 'audit proof'],
    );

    expect($relation)->toBeInstanceOf(EvidenceRelation::class)
        ->and($relation->related_id)->toBe($control->id);

    Event::assertDispatched('eloquent.created: '.EvidenceRelation::class);
});

test('it reports overdue review metadata', function (): void {
    $evidence = Evidence::factory()->create([
        'next_review_at' => now()->subDay(),
    ]);

    expect($evidence->isReviewOverdue())->toBeTrue();
});

test('it is not overdue when next review is not set', function (): void {
    $evidence = Evidence::factory()->create(['next_review_at' => null]);

    expect($evidence->isReviewOverdue())->toBeFalse();
});

test('it can update evidence', function (): void {
    $evidence = Evidence::factory()->create(['title' => 'Old title']);

    $updated = Trustbird::evidence()->update(
        evidence: $evidence,
        title: 'Updated title',
        status: EvidenceStatus::Archived,
    );

    expect($updated->title)->toBe('Updated title')
        ->and($updated->status)->toBe(EvidenceStatus::Archived);
});

test('it covers evidence and relation model methods and factories', function (): void {
    $owner = Person::factory()->create();
    $reviewer = Person::factory()->create(['workspace_id' => $owner->workspace_id]);
    $control = Control::factory()->create(['workspace_id' => $owner->workspace_id]);

    $evidence = Evidence::factory()->active()->create([
        'workspace_id' => $owner->workspace_id,
        'owner_id' => $owner->id,
        'reviewer_id' => $reviewer->id,
        'metadata' => ['sensitivity' => 'internal'],
        'next_review_at' => now()->addDay(),
    ]);

    $relation = EvidenceRelation::factory()->create([
        'workspace_id' => $evidence->workspace_id,
        'evidence_id' => $evidence->id,
        'related_type' => $control::class,
        'related_id' => $control->id,
        'metadata' => ['purpose' => 'audit'],
    ]);

    expect($evidence->relations)->toHaveCount(1);
    expect($evidence->owner)->toBeInstanceOf(Person::class);
    expect($evidence->reviewer)->toBeInstanceOf(Person::class);
    expect($evidence->isReviewOverdue())->toBeFalse();
    expect($relation->evidence)->toBeInstanceOf(Evidence::class);
    expect($relation->related)->toBeInstanceOf(Control::class);

    expect(Evidence::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Evidence\EvidenceFactory::class);
    expect(EvidenceRelation::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Evidence\EvidenceRelationFactory::class);
});
