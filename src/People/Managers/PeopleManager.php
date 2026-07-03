<?php

declare(strict_types=1);

namespace Trustbird\People\Managers;

use DateTimeInterface;
use Trustbird\People\Actions\MarkPersonnelTaskComplete;
use Trustbird\People\Actions\RecordPersonnelReminder;
use Trustbird\People\Actions\TerminatePerson;
use Trustbird\People\Contracts\HasPeople;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;

final readonly class PeopleManager
{
    public function create(
        string $firstName,
        string $lastName,
        string $email,
        EmploymentType $employmentType = EmploymentType::Employee,
        EmploymentStatus $employmentStatus = EmploymentStatus::Active,
        ?DateTimeInterface $startedAt = null,
        ?DateTimeInterface $endedAt = null,
        array $metadata = [],
        ?string $workspaceId = null,
    ): HasPeople {
        /** @var HasPeople $model */
        $model = app(HasPeople::class);

        return $model->query()->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'employment_type' => $employmentType,
            'employment_status' => $employmentStatus,
            'started_at' => $startedAt ?? now(),
            'ended_at' => $endedAt,
            'metadata' => $metadata,
            'workspace_id' => $workspaceId,
        ]);
    }

    public function update(
        HasPeople $person,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        ?EmploymentType $employmentType = null,
        ?EmploymentStatus $employmentStatus = null,
        ?DateTimeInterface $startedAt = null,
        ?DateTimeInterface $endedAt = null,
        ?array $metadata = null,
    ): HasPeople {
        $attributes = array_filter([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'employment_type' => $employmentType,
            'employment_status' => $employmentStatus,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'metadata' => $metadata,
        ], fn ($value) => $value !== null);

        $person->update($attributes);

        return $person;
    }

    public function terminate(HasPeople $person): HasPeople
    {
        return app(TerminatePerson::class)->handle($person);
    }

    public function recordReminder(
        HasPeople $person,
        string $type,
        DateTimeInterface $remindAt,
        array $metadata = [],
    ): void {
        app(RecordPersonnelReminder::class)->handle($person, [
            'type' => $type,
            'remind_at' => $remindAt,
            'metadata' => $metadata,
        ]);
    }

    public function markTaskComplete(
        HasPeople $person,
        string $task,
        ?DateTimeInterface $completedAt = null,
        array $metadata = [],
    ): void {
        app(MarkPersonnelTaskComplete::class)->handle($person, [
            'task' => $task,
            'completed_at' => $completedAt ?? now(),
            'metadata' => $metadata,
        ]);
    }
}
