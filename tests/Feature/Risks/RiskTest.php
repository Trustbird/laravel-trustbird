<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\People\Models\Person;
use Trustbird\Risks\Actions\CreateRisk;
use Trustbird\Risks\Actions\ReviewRisk;
use Trustbird\Risks\Actions\UpdateRisk;
use Trustbird\Risks\Enums\RiskLevel;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;
use Trustbird\Risks\Events\RiskCreated;
use Trustbird\Risks\Events\RiskReviewed;
use Trustbird\Risks\Events\RiskUpdated;
use Trustbird\Risks\Models\Risk;

test('it can create a risk and dispatches event', function () {
    Event::fake();

    $person = Person::factory()->create();

    $action = new CreateRisk();
    $risk = $action->handle([
        'title' => 'Laptop theft during travel',
        'description' => 'Employees travel with company laptops that may be lost or stolen.',
        'owner_id' => $person->id,
        'likelihood' => RiskLevel::Medium,
        'impact' => RiskLevel::High,
    ]);

    expect($risk)->toBeInstanceOf(Risk::class)
        ->title->toBe('Laptop theft during travel')
        ->status->toBe(RiskStatus::Open)
        ->owner_id->toBe($person->id)
        ->likelihood->toBe(RiskLevel::Medium)
        ->impact->toBe(RiskLevel::High);

    expect($risk->owner)->toBeInstanceOf(Person::class);

    Event::assertDispatched(RiskCreated::class, function ($event) use ($risk) {
        return $event->risk->id === $risk->id;
    });
});

test('it can update a risk and dispatches event', function () {
    Event::fake();

    $risk = Risk::factory()->create([
        'title' => 'Old title',
        'status' => RiskStatus::Open,
    ]);

    $action = new UpdateRisk();
    $updatedRisk = $action->handle($risk, [
        'title' => 'Updated title',
        'status' => RiskStatus::BeingAddressed,
        'treatment' => RiskTreatment::Reduce,
    ]);

    expect($updatedRisk->title)->toBe('Updated title')
        ->and($updatedRisk->status)->toBe(RiskStatus::BeingAddressed)
        ->and($updatedRisk->treatment)->toBe(RiskTreatment::Reduce);

    Event::assertDispatched(RiskUpdated::class, function ($event) use ($risk) {
        return $event->risk->id === $risk->id;
    });
});

test('it can review a risk and dispatches event', function () {
    Event::fake();

    $risk = Risk::factory()->underReview()->create();
    $reviewedAt = now()->subHour();
    $nextReviewAt = now()->addMonths(3);

    $action = new ReviewRisk();
    $reviewedRisk = $action->handle($risk, [
        'reviewed_at' => $reviewedAt,
        'next_review_at' => $nextReviewAt,
        'status' => RiskStatus::Accepted,
        'treatment' => RiskTreatment::Accept,
        'likelihood' => RiskLevel::Low,
        'impact' => RiskLevel::Medium,
    ]);

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

    $reviewedRisk = app(ReviewRisk::class)->handle($risk, [
        'status' => RiskStatus::UnderReview,
    ]);

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

    expect($risk->reviewed_at)->toBeInstanceOf(\Carbon\CarbonInterface::class);
    expect($risk->next_review_at)->toBeInstanceOf(\Carbon\CarbonInterface::class);

    expect(Risk::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Risk\RiskFactory::class);
});
