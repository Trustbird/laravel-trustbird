<?php

declare(strict_types=1);

namespace Trustbird\Workspaces\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Trustbird\Assets\Models\Asset;
use Trustbird\Database\Factories\Workspace\WorkspaceFactory;
use Trustbird\People\Models\Person;

final class Workspace extends Model
{
    use HasFactory;
    use HasUlids;

    protected $table = 'workspaces';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): WorkspaceFactory
    {
        return WorkspaceFactory::new();
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
