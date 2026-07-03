<?php

declare(strict_types=1);

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Event;
use Trustbird\Database\Factories\Risk\RiskFactory;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;
use Trustbird\Risks\Enums\RiskLevel;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;
use Trustbird\Risks\Events\RiskReviewed;
use Trustbird\Risks\Models\Risk;

test('it can create a risk and dispatches event', function () {
    Event::fake();

    $person = Person::factory()->create();

    $risk = Trustbird::risks()->create(
        title: 'Laptop theft during travel',
        description: 'Employees travel with company laptops that may be lost or stolen.',
        ownerId: $person->id,
        likelihood: RiskLevel::Medium,
        impact: RiskLevel::High,
    );

    expect($risk)->toBeInstanceOf(Risk::class)
        ->title->toBe('Laptop theft during travel')
        ->status->toBe(RiskStatus::Open)
        ->owner_id->toBe($person->id)
        ->likelihood->toBe(RiskLevel::Medium)
        ->impact->toBe(RiskLevel::High);

    expect($risk->owner)->toBeInstanceOf(Person::class);

    Event::assertDispatched('eloquent.created: '.Risk::class);
});

test('it can update a risk and dispatches event', function () {
    Event::fake();

    $risk = Risk::factory()->create([
        'title' => 'Old title',
        'status' => RiskStatus::Open,
    ]);

    $updatedRisk = Trustbird::risks()->update($risk,
        title: 'Updated title',
        status: RiskStatus::BeingAddressed,
        treatment: RiskTreatment::Reduce,
    );

    expect($updatedRisk->title)->toBe('Updated title')
        ->and($updatedRisk->status)->toBe(RiskStatus::BeingAddressed)
        ->and($updatedRisk->treatment)->toBe(RiskTreatment::Reduce);

    Event::assertDispatched('eloquent.updated: '.Risk::class);
});

test('it can review a risk and dispatches event', function () {
    Event::fake();

    $risk = Risk::factory()->underReview()->create();
    $reviewedAt = now()->subHour();
    $nextReviewAt = now()->addMonths(3);

    $reviewedRisk = Trustbird::risks()->review($risk,
        reviewedAt: $reviewedAt,
        nextReviewAt: $nextReviewAt,
        status: RiskStatus::Accepted,
        treatment: RiskTreatment::Accept,
        likelihood: RiskLevel::Low,
        impact: RiskLevel::Medium,
    );

    expect($reviewedRisk->status)->toBe(RiskStatus::Accepted)
        ->and($reviewedRisk->treatment)->toBe(RiskTreatment::Accept)
        ->and($reviewedRisk->likelihood)->toBe(RiskLevel::Low)
        ->and($reviewedRisk->impact)->toBe(RiskLevel::Medium)
        ->and($reviewedRisk->reviewed_at->toDateTimeString())->toBe($reviewedAt->toDateTimeString())
        ->and($reviewedRisk->next_review_at->toDateTimeString())->toBe($nextReviewAt->toDateTimeString());

    Event::assertDispatched(RiskReviewed::class, function ($event) use ($risk) {
        return $event->risk->id === $risk->id;
    });
});

test('it sets reviewed_at automatically when reviewing a risk', function () {
    $risk = Risk::factory()->create();

    $reviewedRisk = Trustbird::risks()->review($risk,
        status: RiskStatus::UnderReview,
    );

    expect($reviewedRisk->reviewed_at)->not->toBeNull();
});

test('it can determine risk lifecycle state', function () {
    $openRisk = Risk::factory()->open()->create();
    $resolvedRisk = Risk::factory()->resolved()->create();
    $archivedRisk = Risk::factory()->state(['status' => RiskStatus::Archived])->create();
    $dueRisk = Risk::factory()->dueForReview()->create();
    $scheduledRisk = Risk::factory()->state([
        'status' => RiskStatus::BeingAddressed,
        'next_review_at' => now()->addMonth(),
    ])->create();

    expect($openRisk->isActive())->toBeTrue()
        ->and($openRisk->isResolved())->toBeFalse()
        ->and($openRisk->isArchived())->toBeFalse()
        ->and($openRisk->needsReview())->toBeTrue();

    expect($resolvedRisk->isActive())->toBeFalse()
        ->and($resolvedRisk->isResolved())->toBeTrue()
        ->and($resolvedRisk->needsReview())->toBeFalse();

    expect($archivedRisk->isActive())->toBeFalse()
        ->and($archivedRisk->isArchived())->toBeTrue()
        ->and($archivedRisk->needsReview())->toBeFalse();

    expect($dueRisk->needsReview())->toBeTrue();
    expect($scheduledRisk->needsReview())->toBeFalse();
});

test('it covers all risk model methods', function () {
    $person = Person::factory()->create();
    $risk = Risk::factory()->state([
        'owner_id' => $person->id,
        'metadata' => ['source' => 'team workshop'],
        'reviewed_at' => now(),
        'next_review_at' => now()->addMonth(),
    ])->create();

    expect($risk->owner)->toBeInstanceOf(Person::class)
        ->and($risk->owner->id)->toBe($person->id);

    expect($risk->metadata)->toBeArray()
        ->and($risk->metadata['source'])->toBe('team workshop');

    expect($risk->reviewed_at)->toBeInstanceOf(CarbonInterface::class);
    expect($risk->next_review_at)->toBeInstanceOf(CarbonInterface::class);

    expect(Risk::newFactory())->toBeInstanceOf(RiskFactory::class);
});
