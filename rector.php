<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::CODING_STYLE,
        SetList::TYPE_DECLARATION,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::EARLY_RETURN,
    ]);

    $rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests']);

    $rectorConfig->importNames();
    $rectorConfig->removeUnusedImports();

    $rectorConfig->skip(['*/scoper.php', '*/Source/*', '*/Fixture/*']);
};
