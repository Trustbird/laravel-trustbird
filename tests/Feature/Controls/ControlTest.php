<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Controls\Enums\ControlStatus;
use Trustbird\Controls\Events\ControlApproved;
use Trustbird\Controls\Models\Control;
use Trustbird\Controls\Models\ControlRelation;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;
use Trustbird\Risks\Models\Risk;

beforeEach(fn () => Event::fake());

test('it can register a control and dispatches eloquent event', function (): void {
    $owner = Person::factory()->create();

    $control = Trustbird::controls()->create(
        name: 'Encrypt laptops',
        description: 'All company laptops must use full-disk encryption.',
        status: ControlStatus::Draft,
        ownerId: $owner->id,
    );

    expect($control)->toBeInstanceOf(Control::class)
        ->and($control->name)->toBe('Encrypt laptops')
        ->and($control->status)->toBe(ControlStatus::Draft)
        ->and($control->owner_id)->toBe($owner->id);

    Event::assertDispatched('eloquent.created: '.Control::class);
});

test('it can approve a control and dispatches semantic event', function (): void {
    $control = Control::factory()->create(['status' => ControlStatus::Draft]);
    $nextReviewAt = now()->addYear();

    $approved = Trustbird::controls()->approve(
        control: $control,
        nextReviewAt: $nextReviewAt,
    );

    expect($approved->status)->toBe(ControlStatus::Active)
        ->and($approved->reviewed_at)->not->toBeNull()
        ->and($approved->next_review_at?->toDateTimeString())->toBe($nextReviewAt->toDateTimeString());

    Event::assertDispatched(ControlApproved::class, fn ($event) => $event->control->id === $control->id);
});

test('it can relate a control to another Trustbird object', function (): void {
    $control = Control::factory()->create();
    $risk = Risk::factory()->create(['workspace_id' => $control->workspace_id]);

    $relation = Trustbird::controls()->relate(
        control: $control,
        related: $risk,
        metadata: ['mapping' => 'risk mitigation'],
    );

    expect($relation)->toBeInstanceOf(ControlRelation::class)
        ->and($relation->related_type)->toBe(Risk::class)
        ->and($relation->related_id)->toBe($risk->id);

    Event::assertDispatched('eloquent.created: '.ControlRelation::class);
});

test('it reports overdue review metadata', function (): void {
    $control = Control::factory()->create([
        'next_review_at' => now()->subDay(),
    ]);

    expect($control->isReviewOverdue())->toBeTrue();
});

test('it is not overdue when next review is not set', function (): void {
    $control = Control::factory()->create(['next_review_at' => null]);

    expect($control->isReviewOverdue())->toBeFalse();
});

test('it can update a control', function (): void {
    $control = Control::factory()->create(['name' => 'Old name']);

    $updated = Trustbird::controls()->update(
        control: $control,
        name: 'Updated name',
        status: ControlStatus::Inactive,
    );

    expect($updated->name)->toBe('Updated name')
        ->and($updated->status)->toBe(ControlStatus::Inactive);
});

test('it covers control and relation model methods and factories', function (): void {
    $owner = Person::factory()->create();
    $risk = Risk::factory()->create(['workspace_id' => $owner->workspace_id]);

    $control = Control::factory()->active()->create([
        'workspace_id' => $owner->workspace_id,
        'owner_id' => $owner->id,
        'metadata' => ['source' => 'audit'],
        'next_review_at' => now()->addDay(),
    ]);

    $relation = ControlRelation::factory()->create([
        'workspace_id' => $control->workspace_id,
        'control_id' => $control->id,
        'related_type' => $risk::class,
        'related_id' => $risk->id,
        'metadata' => ['confidence' => 'high'],
    ]);

    expect($control->relations)->toHaveCount(1);
    expect($control->owner)->toBeInstanceOf(Person::class);
    expect($control->isReviewOverdue())->toBeFalse();
    expect($relation->control)->toBeInstanceOf(Control::class);
    expect($relation->related)->toBeInstanceOf(Risk::class);

    expect(Control::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Control\ControlFactory::class);
    expect(ControlRelation::newFactory())->toBeInstanceOf(\Trustbird\Database\Factories\Control\ControlRelationFactory::class);
});
