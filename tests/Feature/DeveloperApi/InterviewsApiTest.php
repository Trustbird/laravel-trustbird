<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Facades\Trustbird;
use Trustbird\Interviews\Models\Interview;

beforeEach(fn () => Event::fake());

it('can create an interview via the facade', function () {
    $interview = Trustbird::interviews()->create(
        title: 'Security readiness interview',
    );

    expect($interview)->toBeInstanceOf(Interview::class)
        ->and($interview->title)->toBe('Security readiness interview');

    Event::assertDispatched('eloquent.created: '.Interview::class);
});
