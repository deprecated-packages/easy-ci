<?php

declare(strict_types=1);

namespace Symplify\EasyCI\StaticDetector\ValueObject;

final class StaticReport
{
    private readonly int $staticCallsCount;

    /**
     * @var StaticClassMethodWithStaticCalls[]
     */
    private array $staticClassMethodsWithStaticCalls = [];

    /**
     * @param StaticClassMethodWithStaticCalls[] $staticClassMethodsWithStaticCalls
     */
    public function __construct(array $staticClassMethodsWithStaticCalls)
    {
        $staticCallsCount = 0;
        foreach ($staticClassMethodsWithStaticCalls as $staticClassMethodWithStaticCall) {
            $staticCallsCount += count($staticClassMethodWithStaticCall->getStaticCalls());
        }

        $this->staticCallsCount = $staticCallsCount;

        // sort from most called, to least called - the latter is easier to remove, so put low-hanging fruit first
        usort(
            $staticClassMethodsWithStaticCalls,
            static fn (StaticClassMethodWithStaticCalls $firstStaticClassMethodWithStaticCalls, StaticClassMethodWithStaticCalls $secondStaticClassMethodWithStaticCalls): int => $secondStaticClassMethodWithStaticCalls->getStaticCallsCount() <=> $firstStaticClassMethodWithStaticCalls->getStaticCallsCount()
        );

        $this->staticClassMethodsWithStaticCalls = $staticClassMethodsWithStaticCalls;
    }

    /**
     * @return StaticClassMethodWithStaticCalls[]
     */
    public function getStaticClassMethodsWithStaticCalls(): array
    {
        return $this->staticClassMethodsWithStaticCalls;
    }

    public function getStaticCallsCount(): int
    {
        return $this->staticCallsCount;
    }

    public function getStaticClassMethodCount(): int
    {
        return count($this->staticClassMethodsWithStaticCalls);
    }
}
