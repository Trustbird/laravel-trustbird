<?php

return [
    /*
     * Trustbird multi-tenancy configuration.
     *
     * When multi-tenancy is enabled (true), all models must have a workspace_id assigned.
     * When disabled (false), a default workspace will be automatically used if none is provided.
     */
    'multi_tenant' => env('TRUSTBIRD_MULTI_TENANT', false),

    /*
     * Custom models.
     *
     * You can replace the default Trustbird models with your own.
     * Your models should implement the corresponding Trustbird contract
     * and use the provided Trustbird trait.
     *
     * Default Trustbird models are 'final' and cannot be extended.
     */
    'models' => [
        'person' => null,
        'workspace' => null,
        'asset' => null,
        'team' => null,
        'risk' => null,
        'policy' => null,
        'incident' => null,
        'incident_note' => null,
        'supplier' => null,
        'supplier_relation' => null,
    ],
];
