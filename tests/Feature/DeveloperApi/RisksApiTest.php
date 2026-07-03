<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;
use Trustbird\Risks\Enums\RiskLevel;
use Trustbird\Risks\Enums\RiskStatus;
use Trustbird\Risks\Enums\RiskTreatment;
use Trustbird\Risks\Events\RiskReviewed;
use Trustbird\Risks\Models\Risk;

beforeEach(fn () => Event::fake());

it('can create a risk via the facade', function () {
    $risk = Trustbird::risks()->create(
        title: 'Data Breach'
    );

    expect($risk)->toBeInstanceOf(Risk::class)
        ->and($risk->title)->toBe('Data Breach')
        ->and($risk->status)->toBe(RiskStatus::Open);

    Event::assertDispatched('eloquent.created: '.Risk::class);
});

it('can update a risk via the facade', function () {
    $risk = Risk::factory()->create(['title' => 'Old Title']);
    $owner = Person::factory()->create();

    $updated = Trustbird::risks()->update(
        $risk,
        title: 'New Title',
        description: 'New Description',
        ownerId: $owner->id,
        status: RiskStatus::Accepted,
        treatment: RiskTreatment::Reduce,
        likelihood: RiskLevel::Low,
        impact: RiskLevel::High,
        metadata: ['source' => 'audit']
    );

    expect($updated->title)->toBe('New Title')
        ->and($updated->description)->toBe('New Description')
        ->and($updated->owner_id)->toBe($owner->id)
        ->and($updated->status)->toBe(RiskStatus::Accepted)
        ->and($updated->treatment)->toBe(RiskTreatment::Reduce)
        ->and($updated->likelihood)->toBe(RiskLevel::Low)
        ->and($updated->impact)->toBe(RiskLevel::High)
        ->and($updated->metadata)->toBe(['source' => 'audit']);

    Event::assertDispatched('eloquent.updated: '.Risk::class);
});

it('can accept a risk via the facade', function () {
    $risk = Risk::factory()->create();

    $accepted = Trustbird::risks()->accept($risk, 'Accepting this risk for testing');

    expect($accepted->status)->toBe(RiskStatus::Accepted);
    Event::assertDispatched(RiskReviewed::class);
});

it('can review a risk via the facade', function () {
    $risk = Risk::factory()->create();

    $reviewed = Trustbird::risks()->review($risk, status: RiskStatus::UnderReview);

    expect($reviewed->status)->toBe(RiskStatus::UnderReview);
    Event::assertDispatched(RiskReviewed::class);
});
