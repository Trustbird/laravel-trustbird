<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\Suppliers\Enums\SupplierCriticality;
use Trustbird\Suppliers\Models\Supplier;

beforeEach(fn () => Event::fake());

it('can create a supplier via the facade', function () {
    $supplier = Trustbird::suppliers()->create(
        name: 'Stripe',
        criticality: SupplierCriticality::High,
    );

    expect($supplier)->toBeInstanceOf(Supplier::class)
        ->and($supplier->name)->toBe('Stripe')
        ->and($supplier->criticality)->toBe(SupplierCriticality::High);

    Event::assertDispatched('eloquent.created: '.Supplier::class);
});

