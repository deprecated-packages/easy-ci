<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit30b9a2557d2fa9c9b4269cdcbe26c2d2
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit30b9a2557d2fa9c9b4269cdcbe26c2d2', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit30b9a2557d2fa9c9b4269cdcbe26c2d2', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit30b9a2557d2fa9c9b4269cdcbe26c2d2::getInitializer($loader));

        $loader->setClassMapAuthoritative(true);
        $loader->register(true);

        $includeFiles = \Composer\Autoload\ComposerStaticInit30b9a2557d2fa9c9b4269cdcbe26c2d2::$files;
        foreach ($includeFiles as $fileIdentifier => $file) {
            composerRequire30b9a2557d2fa9c9b4269cdcbe26c2d2($fileIdentifier, $file);
        }

        return $loader;
    }
}

/**
 * @param string $fileIdentifier
 * @param string $file
 * @return void
 */
function composerRequire30b9a2557d2fa9c9b4269cdcbe26c2d2($fileIdentifier, $file)
{
    if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
        $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;

        require $file;
    }
}
