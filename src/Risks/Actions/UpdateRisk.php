<?php

declare(strict_types=1);

namespace Trustbird\Risks\Actions;

use Trustbird\Risks\Events\RiskUpdated;
use Trustbird\Risks\Models\Risk;

final readonly class UpdateRisk
{
    public function handle(Risk $risk, array $attributes): Risk
    {
        $risk->update($attributes);

        RiskUpdated::dispatch($risk);

        return $risk;
    }
}
