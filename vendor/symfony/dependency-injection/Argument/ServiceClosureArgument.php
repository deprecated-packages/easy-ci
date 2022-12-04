<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202212\Symfony\Component\DependencyInjection\Argument;

use EasyCI202212\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
/**
 * Represents a service wrapped in a memoizing closure.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ServiceClosureArgument implements ArgumentInterface
{
    /**
     * @var mixed[]
     */
    private $values;
    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->values = [$value];
    }
    public function getValues() : array
    {
        return $this->values;
    }
    public function setValues(array $values)
    {
        if ([0] !== \array_keys($values)) {
            throw new InvalidArgumentException('A ServiceClosureArgument must hold one and only one value.');
        }
        $this->values = $values;
    }
}
