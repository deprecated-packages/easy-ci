<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder;

<<<<<<< HEAD
<<<<<<< HEAD
use Symplify\EasyCI\Kernel\EasyCIKernel;
=======
use PHPUnit\Framework\TestCase;
use Symplify\EasyCI\Testing\Finder\TestCaseClassFinder;
>>>>>>> 8d6b2a192 (fixup! fixup! fixup! fixup! misc)
=======
use PHPUnit\Framework\TestCase;
>>>>>>> ef088322a (misc)
use Symplify\EasyCI\Testing\UnitTestFilePathsFinder;
use Symplify\EasyCI\Testing\UnitTestFilter;
use Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder\Fixture\OldSchoolTest;
use Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder\Fixture\RandomTest;

final class UnitTestFilePathsFinderTest extends TestCase
{
    private UnitTestFilePathsFinder $unitTestFilePathsFinder;

    protected function setup(): void
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $this->bootKernel(EasyCIKernel::class);
        $this->unitTestFilePathsFinder = $this->getService(UnitTestFilePathsFinder::class);
=======
        $this->unitTestFilePathsFinder = new UnitTestFilePathsFinder(
            new TestCaseClassFinder(),
            new UnitTestFilter(),
        );
>>>>>>> 8d6b2a192 (fixup! fixup! fixup! fixup! misc)
=======
        $this->unitTestFilePathsFinder = new UnitTestFilePathsFinder();
>>>>>>> ef088322a (misc)
    }

    public function test(): void
    {
        $unitTestFilePaths = $this->unitTestFilePathsFinder->findInDirectories([__DIR__ . '/Fixture']);
        $this->assertCount(2, $unitTestFilePaths);

        $this->assertArrayHasKey(RandomTest::class, $unitTestFilePaths);
        $this->assertArrayHasKey(OldSchoolTest::class, $unitTestFilePaths);
    }
}
