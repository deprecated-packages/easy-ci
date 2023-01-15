<?php

declare(strict_types=1);

namespace Symplify\EasyCI\StaticDetector\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class StaticCollectNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private const ALLOWED_METHOD_NAMES = ['getSubscribedEvents'];

    private ?ClassLike $currentClassLike = null;

    public function __construct(
        private readonly StaticNodeCollector $staticNodeCollector,
    ) {
    }

    public function enterNode(Node $node)
    {
        $this->ensureClassLikeOrStaticCall($node);

        if ($node instanceof ClassMethod) {
            $this->enterClassMethod($node);
        }

        return null;
    }

    private function ensureClassLikeOrStaticCall(Node $node): void
    {
        if ($node instanceof ClassLike) {
            $this->currentClassLike = $node;
        }

        if ($node instanceof StaticCall) {
            if ($this->currentClassLike !== null) {
                $this->staticNodeCollector->addStaticCallInsideClass($node, $this->currentClassLike);
            } else {
                $this->staticNodeCollector->addStaticCall($node);
            }
        }
    }

    private function enterClassMethod(ClassMethod $classMethod): void
    {
        if (! $classMethod->isStatic()) {
            return;
        }

        $classMethodName = (string) $classMethod->name;
        if (in_array($classMethodName, self::ALLOWED_METHOD_NAMES, true)) {
            return;
        }

        if ($this->currentClassLike === null) {
            $errorMessage = sprintf('Class not found for static call "%s"', $classMethodName);
            throw new ShouldNotHappenException($errorMessage);
        }

        $this->staticNodeCollector->addStaticClassMethod($classMethod, $this->currentClassLike);
    }
}
