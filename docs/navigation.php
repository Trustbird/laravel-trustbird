<?php

declare(strict_types=1);

/**
 * Documentation navigation map.
 *
 * Add a new docs folder by:
 * 1. Creating docs/NN-name/*.md files.
 * 2. Adding a section below (introduction + index).
 * 3. Running release prepare (sync runs automatically).
 */
return [
    'introduction' => [
        [
            'id' => 'getting-started',
            'heading' => 'Getting started',
            'description' => 'If you are installing Trustbird for the first time, start with:',
            'directory' => '02-usage',
            'path_prefix' => '../02-usage/',
            'prepend' => [
                ['title' => 'Installation', 'path' => '01-installation.md'],
            ],
        ],
        [
            'id' => 'advanced',
            'heading' => 'Advanced',
            'description' => 'For extending and integrating Trustbird:',
            'directory' => '03-advanced',
            'path_prefix' => '../03-advanced/',
        ],
    ],

    'index' => [
        [
            'id' => 'step-1-getting-started',
            'heading' => '## [Step 1: Getting Started](01-getting-started/00-introduction.md)',
            'static_links' => [
                ['title' => 'Introduction', 'path' => '01-getting-started/00-introduction.md'],
                ['title' => 'Installation', 'path' => '01-getting-started/01-installation.md'],
            ],
        ],
        [
            'id' => 'step-2-usage',
            'heading' => '## [Step 2: Usage](02-usage/01-workspaces.md)',
            'directory' => '02-usage',
            'path_prefix' => '02-usage/',
        ],
        [
            'id' => 'step-3-advanced',
            'heading' => '## [Step 3: Advanced](03-advanced/01-events.md)',
            'directory' => '03-advanced',
            'path_prefix' => '03-advanced/',
        ],
    ],
];
