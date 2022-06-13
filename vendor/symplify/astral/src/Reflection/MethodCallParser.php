<?php

declare (strict_types=1);
namespace EasyCI202206\Symplify\Astral\Reflection;

use EasyCI202206\PhpParser\Node\Expr\MethodCall;
use EasyCI202206\PhpParser\Node\Stmt\ClassMethod;
use EasyCI202206\PHPStan\Analyser\Scope;
use EasyCI202206\PHPStan\Reflection\ClassReflection;
use EasyCI202206\PHPStan\Type\ObjectType;
use EasyCI202206\PHPStan\Type\ThisType;
use EasyCI202206\Symplify\Astral\Naming\SimpleNameResolver;
/**
 * @api
 * @deprecated will be removed in next major release
 */
final class MethodCallParser
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Symplify\Astral\Reflection\ReflectionParser
     */
    private $reflectionParser;
    public function __construct(SimpleNameResolver $simpleNameResolver, ReflectionParser $reflectionParser)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->reflectionParser = $reflectionParser;
    }
    /**
     * @return \PhpParser\Node\Stmt\ClassMethod|null
     */
    public function parseMethodCall(MethodCall $methodCall, Scope $scope)
    {
        $callerType = $scope->getType($methodCall->var);
        if ($callerType instanceof ThisType) {
            $callerType = $callerType->getStaticObjectType();
        }
        if (!$callerType instanceof ObjectType) {
            return null;
        }
        $classReflection = $callerType->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return null;
        }
        $methodName = $this->simpleNameResolver->getName($methodCall->name);
        if ($methodName === null) {
            return null;
        }
        if (!$classReflection->hasNativeMethod($methodName)) {
            return null;
        }
        $extendedMethodReflection = $classReflection->getNativeMethod($methodName);
        return $this->reflectionParser->parsePHPStanMethodReflection($extendedMethodReflection);
    }
}
