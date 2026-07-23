<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Evidence\Enums\EvidenceType;
use Trustbird\Evidence\Models\Evidence;
use Trustbird\Facades\Trustbird;

beforeEach(fn () => Event::fake());

it('can create evidence via the facade', function () {
    $evidence = Trustbird::evidence()->create(
        title: 'SOC 2 report',
        type: EvidenceType::Link,
        externalUrl: 'https://example.com/soc2.pdf',
    );

    expect($evidence)->toBeInstanceOf(Evidence::class)
        ->and($evidence->title)->toBe('SOC 2 report')
        ->and($evidence->external_url)->toBe('https://example.com/soc2.pdf');

    Event::assertDispatched('eloquent.created: '.Evidence::class);
});
