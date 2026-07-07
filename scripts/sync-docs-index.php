<?php

declare(strict_types=1);

/**
 * Sync docs navigation from docs/navigation.php.
 *
 * Used internally by scripts/prepare-release.sh.
 */

$checkOnly = in_array('--check', $argv, true);

$root = dirname(__DIR__);
$docsRoot = $root.'/docs';
$docsIndex = $docsRoot.'/index.md';
$introduction = $docsRoot.'/01-getting-started/00-introduction.md';
$navigationFile = $docsRoot.'/navigation.php';

if (! file_exists($navigationFile)) {
    fwrite(STDERR, "docs/navigation.php not found\n");
    exit(1);
}

/** @var array{introduction: list<array<string, mixed>>, index: list<array<string, mixed>>} $navigation */
$navigation = require $navigationFile;

/**
 * @return array{title: string, filename: string}
 */
function docFileMeta(string $file): array
{
    $contents = file($file, FILE_IGNORE_NEW_LINES) ?: [];
    $title = null;

    foreach ($contents as $line) {
        $trimmed = trim($line);
        if ($trimmed === '') {
            continue;
        }

        if (str_starts_with($trimmed, '# ')) {
            $title = trim(substr($trimmed, 2));
            break;
        }

        if (str_starts_with($trimmed, '## ')) {
            $title = trim(substr($trimmed, 3));
            break;
        }
    }

    if ($title === null) {
        $title = pathinfo($file, PATHINFO_FILENAME);
    }

    return [
        'title' => $title,
        'filename' => basename($file),
    ];
}

/**
 * @param array<string, mixed> $link
 */
function formatLink(array $link): string
{
    return "* [{$link['title']}]({$link['path']})";
}

/**
 * @param list<array{title: string, path: string}> $prepend
 * @return list<string>
 */
function docLinkLines(string $docsRoot, string $directory, string $pathPrefix, array $prepend = []): array
{
    $absoluteDirectory = $docsRoot.'/'.$directory;

    if (! is_dir($absoluteDirectory)) {
        fwrite(STDERR, "Directory not found: docs/{$directory}\n");
        exit(1);
    }

    $lines = array_map(
        static fn (array $link): string => formatLink($link),
        $prepend
    );

    $files = glob($absoluteDirectory.'/*.md') ?: [];
    sort($files, SORT_STRING);

    foreach ($files as $file) {
        $meta = docFileMeta($file);
        $lines[] = formatLink([
            'title' => $meta['title'],
            'path' => $pathPrefix.$meta['filename'],
        ]);
    }

    return $lines;
}

function marker(string $id): string
{
    return '<!-- trustbird:docs-nav:'.$id.':';
}

/**
 * @param list<string> $lines
 */
function replaceMarkedBlock(string $contents, string $id, array $lines, string $separator = "\n"): ?string
{
    $start = marker($id).'start -->';
    $end = marker($id).'end -->';

    $startPos = strpos($contents, $start);
    $endPos = strpos($contents, $end);

    if ($startPos === false || $endPos === false || $endPos < $startPos) {
        return null;
    }

    $block = $start."\n".implode($separator, $lines)."\n".$end;

    return substr($contents, 0, $startPos)
        .$block
        .substr($contents, $endPos + strlen($end));
}

/**
 * @param array<string, mixed> $section
 * @return list<string>
 */
function indexSectionLines(string $docsRoot, array $section): array
{
    if (isset($section['static_links']) && is_array($section['static_links'])) {
        /** @var list<array{title: string, path: string}> $staticLinks */
        $staticLinks = $section['static_links'];

        return array_map(
            static fn (array $link): string => formatLink($link),
            $staticLinks
        );
    }

    $directory = $section['directory'] ?? null;
    $pathPrefix = $section['path_prefix'] ?? null;

    if (! is_string($directory) || ! is_string($pathPrefix)) {
        fwrite(STDERR, 'Index section requires static_links or directory + path_prefix'."\n");
        exit(1);
    }

    return docLinkLines($docsRoot, $directory, $pathPrefix);
}

/**
 * @param array<string, mixed> $section
 */
function introductionSectionText(string $docsRoot, array $section): string
{
    $heading = $section['heading'] ?? null;
    $description = $section['description'] ?? null;
    $directory = $section['directory'] ?? null;
    $pathPrefix = $section['path_prefix'] ?? null;

    if (! is_string($heading) || ! is_string($description) || ! is_string($directory) || ! is_string($pathPrefix)) {
        fwrite(STDERR, 'Introduction section requires heading, description, directory and path_prefix'."\n");
        exit(1);
    }

    /** @var list<array{title: string, path: string}> $prepend */
    $prepend = $section['prepend'] ?? [];

    $lines = docLinkLines($docsRoot, $directory, $pathPrefix, $prepend);

    return "## {$heading}\n\n{$description}\n\n".implode("\n", $lines);
}

$changed = false;

if (file_exists($docsIndex)) {
    $index = file_get_contents($docsIndex);
    if ($index === false) {
        fwrite(STDERR, "Failed to read docs/index.md\n");
        exit(1);
    }

    $updated = $index;

    foreach ($navigation['index'] as $section) {
        $id = $section['id'] ?? null;
        if (! is_string($id)) {
            fwrite(STDERR, "Index section is missing id\n");
            exit(1);
        }

        $lines = indexSectionLines($docsRoot, $section);
        $next = replaceMarkedBlock($updated, $id, $lines);

        if ($next === null) {
            fwrite(STDERR, "Could not locate docs navigation block for index section: {$id}\n");
            exit(1);
        }

        $updated = $next;
    }

    if ($updated !== $index) {
        if ($checkOnly) {
            fwrite(STDERR, "docs/index.md is out of sync with docs/navigation.php\n");
            exit(1);
        }

        file_put_contents($docsIndex, $updated);
        fwrite(STDOUT, "Updated docs/index.md\n");
        $changed = true;
    }
}

if (file_exists($introduction)) {
    $intro = file_get_contents($introduction);
    if ($intro === false) {
        fwrite(STDERR, "Failed to read docs/01-getting-started/00-introduction.md\n");
        exit(1);
    }

    $sections = [];
    foreach ($navigation['introduction'] as $section) {
        $sections[] = introductionSectionText($docsRoot, $section);
    }

    $updated = replaceMarkedBlock($intro, 'introduction-sections', $sections, "\n\n");

    if ($updated === null) {
        fwrite(STDERR, "Could not locate introduction navigation block in docs/01-getting-started/00-introduction.md\n");
        exit(1);
    }

    if ($updated !== $intro) {
        if ($checkOnly) {
            fwrite(STDERR, "docs/01-getting-started/00-introduction.md is out of sync with docs/navigation.php\n");
            exit(1);
        }

        file_put_contents($introduction, $updated);
        fwrite(STDOUT, "Updated docs/01-getting-started/00-introduction.md\n");
        $changed = true;
    }
}

if ($checkOnly) {
    fwrite(STDOUT, "Docs navigation is in sync.\n");
    exit(0);
}

if (! $changed) {
    exit(0);
}
