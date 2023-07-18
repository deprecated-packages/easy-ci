<?php

declare (strict_types=1);
namespace EasyCI202307;

use Symplify\EasyCI\Config\EasyCIConfig;
return static function (EasyCIConfig $easyCIConfig) : void {
    $easyCIConfig->typesToSkip([]);
};
