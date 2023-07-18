<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\HttpFoundation\Test\Constraint;

use EasyCI202307\PHPUnit\Framework\Constraint\Constraint;
use EasyCI202307\Symfony\Component\HttpFoundation\Response;
final class ResponseStatusCodeSame extends Constraint
{
    /**
     * @var int
     */
    private $statusCode;
    public function __construct(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }
    public function toString() : string
    {
        return 'status code is ' . $this->statusCode;
    }
    /**
     * @param Response $response
     */
    protected function matches($response) : bool
    {
        return $this->statusCode === $response->getStatusCode();
    }
    /**
     * @param Response $response
     */
    protected function failureDescription($response) : string
    {
        return 'the Response ' . $this->toString();
    }
    /**
     * @param Response $response
     */
    protected function additionalFailureDescription($response) : string
    {
        return (string) $response;
    }
}
