<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\HttpFoundation;

/**
 * ChainRequestMatcher verifies that all checks match against a Request instance.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ChainRequestMatcher implements RequestMatcherInterface
{
    /**
     * @var iterable<RequestMatcherInterface>
     */
    private $matchers;
    /**
     * @param iterable<RequestMatcherInterface> $matchers
     */
    public function __construct(iterable $matchers)
    {
        $this->matchers = $matchers;
    }
    public function matches(Request $request) : bool
    {
        foreach ($this->matchers as $matcher) {
            if (!$matcher->matches($request)) {
                return \false;
            }
        }
        return \true;
    }
}
