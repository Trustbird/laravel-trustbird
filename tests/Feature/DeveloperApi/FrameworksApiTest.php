<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\Frameworks\Models\Framework;

beforeEach(fn () => Event::fake());

it('can create a framework via the facade', function () {
    $framework = Trustbird::frameworks()->create(
        name: 'Operational resilience checklist',
        versionLabel: '1.0',
    );

    expect($framework)->toBeInstanceOf(Framework::class)
        ->and($framework->name)->toBe('Operational resilience checklist')
        ->and($framework->versions)->toHaveCount(1);

    Event::assertDispatched('eloquent.created: '.Framework::class);
});
