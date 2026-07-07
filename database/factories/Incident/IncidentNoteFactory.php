<?php

declare(strict_types=1);

namespace Trustbird\Database\Factories\Incident;

use Illuminate\Database\Eloquent\Factories\Factory;
use Trustbird\Incidents\Models\Incident;
use Trustbird\Incidents\Models\IncidentNote;
use Trustbird\People\Models\Person;
use Trustbird\Workspaces\Models\Workspace;

/**
 * @extends Factory<IncidentNote>
 */
final class IncidentNoteFactory extends Factory
{
    protected $model = IncidentNote::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'incident_id' => Incident::factory(),
            'author_id' => Person::factory(),
            'occurred_at' => $this->faker->dateTimeBetween('-2 days', 'now'),
            'body' => $this->faker->sentence(),
            'metadata' => [],
        ];
    }
}

