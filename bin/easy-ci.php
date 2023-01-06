<?php

declare (strict_types=1);
namespace EasyCI202301;

use Symplify\EasyCI\Kernel\EasyCIKernel;
use EasyCI202301\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
$possibleAutoloadPaths = [
    // dependency
    __DIR__ . '/../../../autoload.php',
    // after split package
    __DIR__ . '/../vendor/autoload.php',
    // monorepo
    __DIR__ . '/../../../vendor/autoload.php',
];
foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (\file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;
        break;
    }
}
$extraConfigs = [];
$easyCIFilePath = \getcwd() . \DIRECTORY_SEPARATOR . 'easy-ci.php';
if (\file_exists($easyCIFilePath)) {
    $extraConfigs[] = $easyCIFilePath;
}
$scoperAutoloadFilepath = __DIR__ . '/../vendor/scoper-autoload.php';
if (\file_exists($scoperAutoloadFilepath)) {
    require_once $scoperAutoloadFilepath;
}
$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(EasyCIKernel::class, $extraConfigs);
$kernelBootAndApplicationRun->run();
