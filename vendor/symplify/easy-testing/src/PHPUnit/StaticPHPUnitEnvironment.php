<?php

declare (strict_types=1);
namespace EasyCI20220611\Symplify\EasyTesting\PHPUnit;

/**
 * @api
 */
final class StaticPHPUnitEnvironment
{
    /**
     * Never ever used static methods if not neccesary, this is just handy for tests + src to prevent duplication.
     */
    public static function isPHPUnitRun() : bool
    {
        return \defined('EasyCI20220611\\PHPUNIT_COMPOSER_INSTALL') || \defined('EasyCI20220611\\__PHPUNIT_PHAR__');
    }
}
