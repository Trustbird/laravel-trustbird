# Workspaces

Workspaces are the primary building block for multi-tenancy in Trustbird. They allow you to group people, assets, and other resources into distinct organizational units.

## Data model

A workspace contains:

* Name
* Slug (unique identifier)
* Description
* Additional metadata

## Creating a workspace

To create a workspace, use the `Trustbird` facade.

```php
use Trustbird\Facades\Trustbird;

$workspace = Trustbird::workspaces()->create(
    name: 'Acme Corp',
    slug: 'acme-corp',
    description: 'Main workspace for Acme Corp',
);
```

## Updating a workspace

```php
use Trustbird\Facades\Trustbird;

$workspace = Trustbird::workspaces()->update($workspace, [
    'name' => 'Acme Corporation',
]);
```

## Future roadmap

Future releases will expand the Workspaces domain with:

* Workspace-level settings
* Resource isolation
* User permissions and roles per workspace
* Usage quotas
