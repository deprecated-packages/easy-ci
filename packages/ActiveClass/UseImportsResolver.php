<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass;

use EasyCI202301\Nette\Utils\FileSystem;
use EasyCI202301\PhpParser\NodeTraverser;
use EasyCI202301\PhpParser\Parser;
use Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator;
use Symplify\EasyCI\ActiveClass\NodeVisitor\UsedClassNodeVisitor;
use EasyCI202301\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCI\Tests\ActiveClass\UseImportsResolver\UseImportsResolverTest
 */
final class UseImportsResolver
{
    /**
     * @readonly
     * @var \PhpParser\Parser
     */
    private $parser;
    /**
     * @readonly
     * @var \Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator
     */
    private $fullyQualifiedNameNodeDecorator;
    public function __construct(Parser $parser, FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator)
    {
        $this->parser = $parser;
        $this->fullyQualifiedNameNodeDecorator = $fullyQualifiedNameNodeDecorator;
    }
    /**
     * @param string[] $filePaths
     * @return string[]
     *@api
     */
    public function resolveFromFilePaths(array $filePaths) : array
    {
        $usedNames = [];
        foreach ($filePaths as $filePath) {
            $usedNames = \array_merge($usedNames, $this->resolve($filePath));
        }
        $usedNames = \array_unique($usedNames);
        \sort($usedNames);
        return $usedNames;
    }
    /**
     * @return string[]
     */
    public function resolve(string $filePath) : array
    {
        $fileContents = FileSystem::read($filePath);
        $stmts = $this->parser->parse($fileContents);
        if ($stmts === null) {
            return [];
        }
        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);
        $nodeTraverser = new NodeTraverser();
        $usedClassNodeVisitor = new UsedClassNodeVisitor();
        $nodeTraverser->addVisitor($usedClassNodeVisitor);
        $nodeTraverser->traverse($stmts);
        return $usedClassNodeVisitor->getUsedNames();
    }
}
