<?php

namespace EasyCI202401\Illuminate\Contracts\Container;

use Exception;
use EasyCI202401\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
