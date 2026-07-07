<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Incident;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Incidents\Enums\IncidentSeverity;
use Trustbird\Incidents\Enums\IncidentStatus;
use Trustbird\Incidents\Models\Incident;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<Incident>
 */
final class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'severity' => $this->faker->randomElement(IncidentSeverity::cases()),
            'status' => IncidentStatus::Open,
            'owner_id' => Person::factory(),
            'responder_id' => Person::factory(),
            'detected_at' => $this->faker->dateTimeBetween('-2 weeks', 'now'),
            'metadata' => [],
        ];
    }

    public function open(): self
    {
        return $this->state(fn () => [
            'status' => IncidentStatus::Open,
        ]);
    }

    public function resolved(): self
    {
        return $this->state(fn () => [
            'status' => IncidentStatus::Resolved,
            'resolved_at' => now(),
        ]);
    }
}

