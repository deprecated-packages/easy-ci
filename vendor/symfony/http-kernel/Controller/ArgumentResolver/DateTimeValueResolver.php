<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use EasyCI202307\Symfony\Component\HttpFoundation\Request;
use EasyCI202307\Symfony\Component\HttpKernel\Attribute\MapDateTime;
use EasyCI202307\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use EasyCI202307\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use EasyCI202307\Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
/**
 * Convert DateTime instances from request attribute variable.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author Tim Goudriaan <tim@codedmonkey.com>
 */
final class DateTimeValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument) : bool
    {
        return \is_a($argument->getType(), \DateTimeInterface::class, \true) && $request->attributes->has($argument->getName());
    }
    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument) : iterable
    {
        $value = $request->attributes->get($argument->getName());
        $class = \DateTimeInterface::class === $argument->getType() ? \DateTimeImmutable::class : $argument->getType();
        if ($value instanceof \DateTimeInterface) {
            $className = $class;
            (yield $value instanceof $className ? $value : $class::createFromInterface($value));
            return;
        }
        if ($argument->isNullable() && !$value) {
            (yield null);
            return;
        }
        $format = null;
        if ($attributes = $argument->getAttributes(MapDateTime::class, ArgumentMetadata::IS_INSTANCEOF)) {
            $attribute = $attributes[0];
            $format = $attribute->format;
        }
        if (null !== $format) {
            $date = $class::createFromFormat($format, $value);
            if (($class::getLastErrors() ?: ['warning_count' => 0])['warning_count']) {
                $date = \false;
            }
        } else {
            if (\false !== \filter_var($value, \FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]])) {
                $value = '@' . $value;
            }
            try {
                $date = new $class($value ?? 'now');
            } catch (\Exception $exception) {
                $date = \false;
            }
        }
        if (!$date) {
            throw new NotFoundHttpException(\sprintf('Invalid date given for parameter "%s".', $argument->getName()));
        }
        (yield $date);
    }
}
