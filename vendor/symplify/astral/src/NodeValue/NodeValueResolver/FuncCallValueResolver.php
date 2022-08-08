<?php

declare (strict_types=1);
namespace EasyCI202208\Symplify\Astral\NodeValue\NodeValueResolver;

use EasyCI202208\PhpParser\ConstExprEvaluator;
use EasyCI202208\PhpParser\Node\Expr;
use EasyCI202208\PhpParser\Node\Expr\FuncCall;
use EasyCI202208\PhpParser\Node\Name;
use EasyCI202208\Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
use EasyCI202208\Symplify\Astral\Exception\ShouldNotHappenException;
/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 *
 * @implements NodeValueResolverInterface<FuncCall>
 */
final class FuncCallValueResolver implements NodeValueResolverInterface
{
    /**
     * @var string[]
     */
    private const EXCLUDED_FUNC_NAMES = ['pg_*'];
    /**
     * @var \PhpParser\ConstExprEvaluator
     */
    private $constExprEvaluator;
    public function __construct(ConstExprEvaluator $constExprEvaluator)
    {
        $this->constExprEvaluator = $constExprEvaluator;
    }
    public function getType() : string
    {
        return FuncCall::class;
    }
    /**
     * @param FuncCall $expr
     * @return mixed
     */
    public function resolve(Expr $expr, string $currentFilePath)
    {
        if ($expr->name instanceof Name && $expr->name->toString() === 'getcwd') {
            return \dirname($currentFilePath);
        }
        $args = $expr->getArgs();
        $arguments = [];
        foreach ($args as $arg) {
            $arguments[] = $this->constExprEvaluator->evaluateDirectly($arg->value);
        }
        if ($expr->name instanceof Name) {
            $functionName = (string) $expr->name;
            if (!$this->isAllowedFunctionName($functionName)) {
                return null;
            }
            if (\function_exists($functionName)) {
                return $functionName(...$arguments);
            }
            throw new ShouldNotHappenException();
        }
        return null;
    }
    private function isAllowedFunctionName(string $functionName) : bool
    {
        foreach (self::EXCLUDED_FUNC_NAMES as $excludedFuncName) {
            if (\fnmatch($excludedFuncName, $functionName, \FNM_NOESCAPE)) {
                return \false;
            }
        }
        return \true;
    }
}
