<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Database\Factories\Supplier\SupplierFactory;
use Trustbird\Database\Factories\Supplier\SupplierRelationFactory;
use Trustbird\Facades\Trustbird;
use Trustbird\People\Models\Person;
use Trustbird\Risks\Models\Risk;
use Trustbird\Suppliers\Enums\SupplierCriticality;
use Trustbird\Suppliers\Enums\SupplierStatus;
use Trustbird\Suppliers\Models\Supplier;
use Trustbird\Suppliers\Models\SupplierRelation;

beforeEach(fn () => Event::fake());

test('it can register a supplier and dispatches eloquent event', function (): void {
    $owner = Person::factory()->create();

    $supplier = Trustbird::suppliers()->create(
        name: 'Acme Hosting',
        description: 'Primary hosting provider.',
        status: SupplierStatus::Active,
        criticality: SupplierCriticality::High,
        ownerId: $owner->id,
    );

    expect($supplier)->toBeInstanceOf(Supplier::class)
        ->and($supplier->name)->toBe('Acme Hosting')
        ->and($supplier->status)->toBe(SupplierStatus::Active)
        ->and($supplier->criticality)->toBe(SupplierCriticality::High)
        ->and($supplier->owner_id)->toBe($owner->id);

    expect($supplier->owner)->toBeInstanceOf(Person::class);

    Event::assertDispatched('eloquent.created: '.Supplier::class);
});

test('it can review a supplier', function (): void {
    $supplier = Supplier::factory()->create([
        'reviewed_at' => null,
        'next_review_at' => now()->subDay(),
    ]);

    expect($supplier->isReviewOverdue())->toBeTrue();

    $reviewedAt = now();
    $nextReviewAt = now()->addMonths(6);

    $reviewed = Trustbird::suppliers()->review(
        supplier: $supplier,
        reviewedAt: $reviewedAt,
        nextReviewAt: $nextReviewAt,
    );

    expect($reviewed->reviewed_at?->toDateTimeString())->toBe($reviewedAt->toDateTimeString());
    expect($reviewed->next_review_at?->toDateTimeString())->toBe($nextReviewAt->toDateTimeString());

    Event::assertDispatched('eloquent.updated: '.Supplier::class);
});

test('it is not overdue when next review is not set', function (): void {
    $supplier = Supplier::factory()->create([
        'next_review_at' => null,
    ]);

    expect($supplier->isReviewOverdue())->toBeFalse();
});

test('it can relate a supplier to a canonical Trustbird object and dispatches eloquent event', function (): void {
    $supplier = Supplier::factory()->create();
    $risk = Risk::factory()->create(['workspace_id' => $supplier->workspace_id]);

    $relation = Trustbird::suppliers()->relate(
        supplier: $supplier,
        related: $risk,
        metadata: ['reason' => 'supplier risk linkage'],
    );

    expect($relation)->toBeInstanceOf(SupplierRelation::class)
        ->and($relation->supplier_id)->toBe($supplier->id)
        ->and($relation->related_type)->toBe(Risk::class)
        ->and($relation->related_id)->toBe($risk->id);

    expect($relation->supplier)->toBeInstanceOf(Supplier::class);
    expect($relation->related)->toBeInstanceOf(Risk::class);

    Event::assertDispatched('eloquent.created: '.SupplierRelation::class);
});

test('it covers supplier and relation model methods and factories', function (): void {
    $owner = Person::factory()->create();
    $risk = Risk::factory()->create(['workspace_id' => $owner->workspace_id]);

    $supplier = Supplier::factory()->create([
        'workspace_id' => $owner->workspace_id,
        'owner_id' => $owner->id,
        'metadata' => ['source' => 'inventory'],
        'next_review_at' => now()->addDay(),
    ]);

    $relation = SupplierRelation::factory()->create([
        'workspace_id' => $supplier->workspace_id,
        'supplier_id' => $supplier->id,
        'related_type' => $risk::class,
        'related_id' => $risk->id,
        'metadata' => ['confidence' => 'high'],
    ]);

    expect($supplier->relations)->toHaveCount(1)->and($supplier->relations->first()->id)->toBe($relation->id);
    expect($supplier->owner)->toBeInstanceOf(Person::class);
    expect($supplier->isReviewOverdue())->toBeFalse();

    expect($supplier->metadata)->toBeArray()->and($supplier->metadata['source'])->toBe('inventory');
    expect($relation->metadata)->toBeArray()->and($relation->metadata['confidence'])->toBe('high');

    expect(Supplier::newFactory())->toBeInstanceOf(SupplierFactory::class);
    expect(SupplierRelation::newFactory())->toBeInstanceOf(SupplierRelationFactory::class);
});

