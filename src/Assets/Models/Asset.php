<?php

declare(strict_types=1);

namespace Trustbird\Assets\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Trustbird\Assets\Enums\AssetKind;
use Trustbird\Database\Factories\Asset\AssetFactory;
use Trustbird\People\Models\Person;

final class Asset extends Model
{
    use HasFactory;
    use HasUlids;

    protected $table = 'assets';

    protected $fillable = [
        'name',
        'description',
        'kind',
        'owner_id',
        'provider_name',
        'external_reference',
        'environment',
        'criticality',
        'contains_personal_data',
        'contains_sensitive_data',
        'status',
        'acquired_at',
        'retired_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'kind' => AssetKind::class,
            'contains_personal_data' => 'boolean',
            'contains_sensitive_data' => 'boolean',
            'acquired_at' => 'datetime',
            'retired_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): AssetFactory
    {
        return AssetFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'owner_id');
    }

    public function isDevice(): bool
    {
        return $this->kind === AssetKind::Device;
    }

    public function isSystem(): bool
    {
        return $this->kind === AssetKind::System;
    }

    public function isDataCarrier(): bool
    {
        return in_array($this->kind, [
            AssetKind::Device,
            AssetKind::System,
            AssetKind::Application,
            AssetKind::DataStore,
            AssetKind::Service,
            AssetKind::Account,
        ], true);
    }
}