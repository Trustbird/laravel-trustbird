# People

The People domain stores individuals that are part of an organisation's trust and compliance scope.

Examples include:

* Employees
* Contractors
* Freelancers
* Advisors
* Interns

People form the foundation for many Trustbird workflows, including onboarding, offboarding, access reviews, policy acknowledgements and evidence ownership.

## Data model

A person contains:

* First name
* Last name
* Email address
* Employment type
* Employment status
* Start date
* End date
* Additional metadata

## Employment types

Supported employment types:

* Employee
* Contractor
* Freelancer
* Advisor
* Intern

## Employment status lifecycle

A person typically moves through the following lifecycle:

```text
Pending → Active → Offboarding → Terminated
```

## Creating a person

```php
use Trustbird\People\Actions\CreatePerson;
use Trustbird\People\Enums\EmploymentStatus;
use Trustbird\People\Enums\EmploymentType;

$person = app(CreatePerson::class)->handle([
    'first_name' => 'Jane',
    'last_name' => 'Doe',
    'email' => 'jane@example.com',
    'employment_type' => EmploymentType::Employee,
    'employment_status' => EmploymentStatus::Active,
]);
```

## Terminating a person

```php
use Trustbird\People\Actions\TerminatePerson;

app(TerminatePerson::class)->handle($person);
```

## Completing tasks and reminders

You can mark tasks as complete and record reminders for personnel using dedicated actions.

### Mark task complete

```php
use Trustbird\People\Actions\MarkPersonnelTaskComplete;

app(MarkPersonnelTaskComplete::class)->handle($person, [
    'task' => 'equipment_provisioning',
    'completed_at' => now(),
]);
```

### Record reminder

```php
use Trustbird\People\Actions\RecordPersonnelReminder;

app(RecordPersonnelReminder::class)->handle($person, [
    'type' => 'contract_renewal',
    'remind_at' => now()->addYear(),
]);
```

## Future roadmap

Future releases will expand the People domain with:

* Onboarding workflows
* Offboarding workflows
* Access reviews
* Training records
* Policy acknowledgements
* Identity provider synchronisation
* Asset ownership
