<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\HttpKernel\Controller;

use EasyCI202307\Symfony\Component\HttpFoundation\Request;
use EasyCI202307\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
/**
 * Responsible for resolving the value of an argument based on its metadata.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
interface ArgumentValueResolverInterface
{
    /**
     * Whether this resolver can resolve the value for the given ArgumentMetadata.
     */
    public function supports(Request $request, ArgumentMetadata $argument) : bool;
    /**
     * Returns the possible value(s).
     */
    public function resolve(Request $request, ArgumentMetadata $argument) : iterable;
}
