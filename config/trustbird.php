<?php

return [
    /*
     * Trustbird multi-tenancy configuration.
     * 
     * When multi-tenancy is enabled (true), all models must have a workspace_id assigned.
     * When disabled (false), a default workspace will be automatically used if none is provided.
     */
    'multi_tenant' => env('TRUSTBIRD_MULTI_TENANT', false),
];
