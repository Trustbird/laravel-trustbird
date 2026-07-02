# Workspaces

Workspaces are the primary building block for multi-tenancy in Trustbird. They allow you to group people, assets, and other resources into distinct organizational units.

## Data model

A workspace contains:

* Name
* Slug (unique identifier)
* Description
* Additional metadata

## Creating a workspace

```php
use Trustbird\Workspaces\Actions\CreateWorkspace;

$workspace = app(CreateWorkspace::class)->handle([
    'name' => 'Acme Corp',
    'slug' => 'acme-corp',
    'description' => 'Main workspace for Acme Corp',
]);
```

## Updating a workspace

```php
use Trustbird\Workspaces\Actions\UpdateWorkspace;

$workspace = app(UpdateWorkspace::class)->handle($workspace, [
    'name' => 'Acme Corporation',
]);
```

## Future roadmap

Future releases will expand the Workspaces domain with:

* Workspace-level settings
* Resource isolation
* User permissions and roles per workspace
* Usage quotas
