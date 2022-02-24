<?php

declare (strict_types=1);
namespace EasyCI20220224\PHPStan\PhpDocParser\Parser;

use EasyCI20220224\PHPStan\PhpDocParser\Ast;
use EasyCI20220224\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer;
class PhpDocParser
{
    private const DISALLOWED_DESCRIPTION_START_TOKENS = [\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_UNION, \EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_INTERSECTION];
    /** @var TypeParser */
    private $typeParser;
    /** @var ConstExprParser */
    private $constantExprParser;
    public function __construct(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TypeParser $typeParser, \EasyCI20220224\PHPStan\PhpDocParser\Parser\ConstExprParser $constantExprParser)
    {
        $this->typeParser = $typeParser;
        $this->constantExprParser = $constantExprParser;
    }
    public function parse(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode
    {
        $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_OPEN_PHPDOC);
        $tokens->tryConsumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_PHPDOC_EOL);
        $children = [];
        if (!$tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_CLOSE_PHPDOC)) {
            $children[] = $this->parseChild($tokens);
            while ($tokens->tryConsumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_PHPDOC_EOL) && !$tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_CLOSE_PHPDOC)) {
                $children[] = $this->parseChild($tokens);
            }
        }
        try {
            $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_CLOSE_PHPDOC);
        } catch (\EasyCI20220224\PHPStan\PhpDocParser\Parser\ParserException $e) {
            $name = '';
            if (\count($children) > 0) {
                $lastChild = $children[\count($children) - 1];
                if ($lastChild instanceof \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode) {
                    $name = $lastChild->name;
                }
            }
            $tokens->forwardToTheEnd();
            return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode([new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode($name, new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode($e->getMessage(), $e))]);
        }
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode(\array_values($children));
    }
    private function parseChild(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocChildNode
    {
        if ($tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_PHPDOC_TAG)) {
            return $this->parseTag($tokens);
        }
        return $this->parseText($tokens);
    }
    private function parseText(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode
    {
        $text = '';
        while (!$tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_PHPDOC_EOL)) {
            $text .= $tokens->getSkippedHorizontalWhiteSpaceIfAny() . $tokens->joinUntil(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_PHPDOC_EOL, \EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_CLOSE_PHPDOC, \EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_END);
            if (!$tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_PHPDOC_EOL)) {
                break;
            }
            $tokens->pushSavePoint();
            $tokens->next();
            if ($tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_PHPDOC_TAG) || $tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_PHPDOC_EOL) || $tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_CLOSE_PHPDOC) || $tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_END)) {
                $tokens->rollback();
                break;
            }
            $tokens->dropSavePoint();
            $text .= "\n";
        }
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode(\trim($text, " \t"));
    }
    public function parseTag(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode
    {
        $tag = $tokens->currentTokenValue();
        $tokens->next();
        $value = $this->parseTagValue($tokens, $tag);
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode($tag, $value);
    }
    public function parseTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens, string $tag) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode
    {
        try {
            $tokens->pushSavePoint();
            switch ($tag) {
                case '@param':
                case '@phpstan-param':
                case '@psalm-param':
                    $tagValue = $this->parseParamTagValue($tokens);
                    break;
                case '@var':
                case '@phpstan-var':
                case '@psalm-var':
                    $tagValue = $this->parseVarTagValue($tokens);
                    break;
                case '@return':
                case '@phpstan-return':
                case '@psalm-return':
                    $tagValue = $this->parseReturnTagValue($tokens);
                    break;
                case '@throws':
                case '@phpstan-throws':
                    $tagValue = $this->parseThrowsTagValue($tokens);
                    break;
                case '@mixin':
                    $tagValue = $this->parseMixinTagValue($tokens);
                    break;
                case '@deprecated':
                    $tagValue = $this->parseDeprecatedTagValue($tokens);
                    break;
                case '@property':
                case '@property-read':
                case '@property-write':
                case '@phpstan-property':
                case '@phpstan-property-read':
                case '@phpstan-property-write':
                case '@psalm-property':
                case '@psalm-property-read':
                case '@psalm-property-write':
                    $tagValue = $this->parsePropertyTagValue($tokens);
                    break;
                case '@method':
                case '@phpstan-method':
                case '@psalm-method':
                    $tagValue = $this->parseMethodTagValue($tokens);
                    break;
                case '@template':
                case '@phpstan-template':
                case '@psalm-template':
                case '@template-covariant':
                case '@phpstan-template-covariant':
                case '@psalm-template-covariant':
                    $tagValue = $this->parseTemplateTagValue($tokens);
                    break;
                case '@extends':
                case '@phpstan-extends':
                case '@template-extends':
                    $tagValue = $this->parseExtendsTagValue('@extends', $tokens);
                    break;
                case '@implements':
                case '@phpstan-implements':
                case '@template-implements':
                    $tagValue = $this->parseExtendsTagValue('@implements', $tokens);
                    break;
                case '@use':
                case '@phpstan-use':
                case '@template-use':
                    $tagValue = $this->parseExtendsTagValue('@use', $tokens);
                    break;
                case '@phpstan-type':
                case '@psalm-type':
                    $tagValue = $this->parseTypeAliasTagValue($tokens);
                    break;
                case '@phpstan-import-type':
                case '@psalm-import-type':
                    $tagValue = $this->parseTypeAliasImportTagValue($tokens);
                    break;
                default:
                    $tagValue = new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode($this->parseOptionalDescription($tokens));
                    break;
            }
            $tokens->dropSavePoint();
        } catch (\EasyCI20220224\PHPStan\PhpDocParser\Parser\ParserException $e) {
            $tokens->rollback();
            $tagValue = new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode($this->parseOptionalDescription($tokens), $e);
        }
        return $tagValue;
    }
    private function parseParamTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode
    {
        $type = $this->typeParser->parse($tokens);
        $isReference = $tokens->tryConsumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_REFERENCE);
        $isVariadic = $tokens->tryConsumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_VARIADIC);
        $parameterName = $this->parseRequiredVariableName($tokens);
        $description = $this->parseOptionalDescription($tokens);
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode($type, $isVariadic, $parameterName, $description, $isReference);
    }
    private function parseVarTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode
    {
        $type = $this->typeParser->parse($tokens);
        $variableName = $this->parseOptionalVariableName($tokens);
        $description = $this->parseOptionalDescription($tokens, $variableName === '');
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode($type, $variableName, $description);
    }
    private function parseReturnTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode
    {
        $type = $this->typeParser->parse($tokens);
        $description = $this->parseOptionalDescription($tokens, \true);
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode($type, $description);
    }
    private function parseThrowsTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode
    {
        $type = $this->typeParser->parse($tokens);
        $description = $this->parseOptionalDescription($tokens, \true);
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode($type, $description);
    }
    private function parseMixinTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\MixinTagValueNode
    {
        $type = $this->typeParser->parse($tokens);
        $description = $this->parseOptionalDescription($tokens, \true);
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\MixinTagValueNode($type, $description);
    }
    private function parseDeprecatedTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\DeprecatedTagValueNode
    {
        $description = $this->parseOptionalDescription($tokens);
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\DeprecatedTagValueNode($description);
    }
    private function parsePropertyTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode
    {
        $type = $this->typeParser->parse($tokens);
        $parameterName = $this->parseRequiredVariableName($tokens);
        $description = $this->parseOptionalDescription($tokens);
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode($type, $parameterName, $description);
    }
    private function parseMethodTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode
    {
        $isStatic = $tokens->tryConsumeTokenValue('static');
        $returnTypeOrMethodName = $this->typeParser->parse($tokens);
        if ($tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER)) {
            $returnType = $returnTypeOrMethodName;
            $methodName = $tokens->currentTokenValue();
            $tokens->next();
        } elseif ($returnTypeOrMethodName instanceof \EasyCI20220224\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode) {
            $returnType = $isStatic ? new \EasyCI20220224\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode('static') : null;
            $methodName = $returnTypeOrMethodName->name;
            $isStatic = \false;
        } else {
            $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER);
            // will throw exception
            exit;
        }
        $parameters = [];
        $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_OPEN_PARENTHESES);
        if (!$tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_CLOSE_PARENTHESES)) {
            $parameters[] = $this->parseMethodTagValueParameter($tokens);
            while ($tokens->tryConsumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_COMMA)) {
                $parameters[] = $this->parseMethodTagValueParameter($tokens);
            }
        }
        $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_CLOSE_PARENTHESES);
        $description = $this->parseOptionalDescription($tokens);
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode($isStatic, $returnType, $methodName, $parameters, $description);
    }
    private function parseMethodTagValueParameter(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueParameterNode
    {
        switch ($tokens->currentTokenType()) {
            case \EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER:
            case \EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_OPEN_PARENTHESES:
            case \EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_NULLABLE:
                $parameterType = $this->typeParser->parse($tokens);
                break;
            default:
                $parameterType = null;
        }
        $isReference = $tokens->tryConsumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_REFERENCE);
        $isVariadic = $tokens->tryConsumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_VARIADIC);
        $parameterName = $tokens->currentTokenValue();
        $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_VARIABLE);
        if ($tokens->tryConsumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_EQUAL)) {
            $defaultValue = $this->constantExprParser->parse($tokens);
        } else {
            $defaultValue = null;
        }
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueParameterNode($parameterType, $isReference, $isVariadic, $parameterName, $defaultValue);
    }
    private function parseTemplateTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode
    {
        $name = $tokens->currentTokenValue();
        $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER);
        if ($tokens->tryConsumeTokenValue('of') || $tokens->tryConsumeTokenValue('as')) {
            $bound = $this->typeParser->parse($tokens);
        } else {
            $bound = null;
        }
        $description = $this->parseOptionalDescription($tokens);
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode($name, $bound, $description);
    }
    private function parseExtendsTagValue(string $tagName, \EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode
    {
        $baseType = new \EasyCI20220224\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode($tokens->currentTokenValue());
        $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER);
        $type = $this->typeParser->parseGeneric($tokens, $baseType);
        $description = $this->parseOptionalDescription($tokens);
        switch ($tagName) {
            case '@extends':
                return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\ExtendsTagValueNode($type, $description);
            case '@implements':
                return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\ImplementsTagValueNode($type, $description);
            case '@use':
                return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\UsesTagValueNode($type, $description);
        }
        throw new \EasyCI20220224\PHPStan\ShouldNotHappenException();
    }
    private function parseTypeAliasTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasTagValueNode
    {
        $alias = $tokens->currentTokenValue();
        $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER);
        // support psalm-type syntax
        $tokens->tryConsumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_EQUAL);
        $type = $this->typeParser->parse($tokens);
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasTagValueNode($alias, $type);
    }
    private function parseTypeAliasImportTagValue(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasImportTagValueNode
    {
        $importedAlias = $tokens->currentTokenValue();
        $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER);
        if (!$tokens->tryConsumeTokenValue('from')) {
            throw new \EasyCI20220224\PHPStan\PhpDocParser\Parser\ParserException($tokens->currentTokenValue(), $tokens->currentTokenType(), $tokens->currentTokenOffset(), \EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER);
        }
        $importedFrom = $tokens->currentTokenValue();
        $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER);
        $importedAs = null;
        if ($tokens->tryConsumeTokenValue('as')) {
            $importedAs = $tokens->currentTokenValue();
            $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER);
        }
        return new \EasyCI20220224\PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasImportTagValueNode($importedAlias, new \EasyCI20220224\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode($importedFrom), $importedAs);
    }
    private function parseOptionalVariableName(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : string
    {
        if ($tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_VARIABLE)) {
            $parameterName = $tokens->currentTokenValue();
            $tokens->next();
        } elseif ($tokens->isCurrentTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_THIS_VARIABLE)) {
            $parameterName = '$this';
            $tokens->next();
        } else {
            $parameterName = '';
        }
        return $parameterName;
    }
    private function parseRequiredVariableName(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : string
    {
        $parameterName = $tokens->currentTokenValue();
        $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_VARIABLE);
        return $parameterName;
    }
    private function parseOptionalDescription(\EasyCI20220224\PHPStan\PhpDocParser\Parser\TokenIterator $tokens, bool $limitStartToken = \false) : string
    {
        if ($limitStartToken) {
            foreach (self::DISALLOWED_DESCRIPTION_START_TOKENS as $disallowedStartToken) {
                if (!$tokens->isCurrentTokenType($disallowedStartToken)) {
                    continue;
                }
                $tokens->consumeTokenType(\EasyCI20220224\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_OTHER);
                // will throw exception
            }
        }
        return $this->parseText($tokens)->text;
    }
}