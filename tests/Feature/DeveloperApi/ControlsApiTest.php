<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Trustbird\Controls\Models\Control;
use Trustbird\Facades\Trustbird;

beforeEach(fn () => Event::fake());

it('can create a control via the facade', function () {
    $control = Trustbird::controls()->create(
        name: 'Backup verification',
    );

    expect($control)->toBeInstanceOf(Control::class)
        ->and($control->name)->toBe('Backup verification');

    Event::assertDispatched('eloquent.created: '.Control::class);
});
