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
use EasyCI202307\Symfony\Component\HttpFoundation\Request;
use EasyCI202307\Symfony\Component\HttpFoundation\Response;
/**
 * Asserts that the response is in the given format.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ResponseFormatSame extends Constraint
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;
    /**
     * @var string|null
     */
    private $format;
    public function __construct(Request $request, ?string $format)
    {
        $this->request = $request;
        $this->format = $format;
    }
    public function toString() : string
    {
        return 'format is ' . ($this->format ?? 'null');
    }
    /**
     * @param Response $response
     */
    protected function matches($response) : bool
    {
        return $this->format === $this->request->getFormat($response->headers->get('Content-Type'));
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
