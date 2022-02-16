<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace EasyCI20220216\Nette\Neon;

/** @internal */
final class Parser
{
    /** @var TokenStream */
    private $tokens;
    public function parse(\EasyCI20220216\Nette\Neon\TokenStream $tokens) : \EasyCI20220216\Nette\Neon\Node
    {
        $this->tokens = $tokens;
        while ($this->tokens->consume(\EasyCI20220216\Nette\Neon\Token::NEWLINE)) {
        }
        $node = $this->parseBlock($this->tokens->getIndentation());
        while ($this->tokens->consume(\EasyCI20220216\Nette\Neon\Token::NEWLINE)) {
        }
        if ($this->tokens->isNext()) {
            $this->tokens->error();
        }
        return $node;
    }
    private function parseBlock(string $indent, bool $onlyBullets = \false) : \EasyCI20220216\Nette\Neon\Node
    {
        $res = new \EasyCI20220216\Nette\Neon\Node\BlockArrayNode($indent, $this->tokens->getPos());
        $keyCheck = [];
        loop:
        $item = new \EasyCI20220216\Nette\Neon\Node\ArrayItemNode($this->tokens->getPos());
        if ($this->tokens->consume('-')) {
            // continue
        } elseif (!$this->tokens->isNext() || $onlyBullets) {
            return $res->items ? $res : new \EasyCI20220216\Nette\Neon\Node\LiteralNode(null, $this->tokens->getPos());
        } else {
            $value = $this->parseValue();
            if ($this->tokens->consume(':', '=')) {
                $this->checkArrayKey($value, $keyCheck);
                $item->key = $value;
            } else {
                if ($res->items) {
                    $this->tokens->error();
                }
                return $value;
            }
        }
        $res->items[] = $item;
        $item->value = new \EasyCI20220216\Nette\Neon\Node\LiteralNode(null, $this->tokens->getPos());
        if ($this->tokens->consume(\EasyCI20220216\Nette\Neon\Token::NEWLINE)) {
            while ($this->tokens->consume(\EasyCI20220216\Nette\Neon\Token::NEWLINE)) {
            }
            $nextIndent = $this->tokens->getIndentation();
            if (\strncmp($nextIndent, $indent, \min(\strlen($nextIndent), \strlen($indent)))) {
                $this->tokens->error('Invalid combination of tabs and spaces');
            } elseif (\strlen($nextIndent) > \strlen($indent)) {
                // open new block
                $item->value = $this->parseBlock($nextIndent);
            } elseif (\strlen($nextIndent) < \strlen($indent)) {
                // close block
                return $res;
            } elseif ($item->key !== null && $this->tokens->isNext('-')) {
                // special dash subblock
                $item->value = $this->parseBlock($indent, \true);
            }
        } elseif ($item->key === null) {
            $item->value = $this->parseBlock($indent . '  ');
            // open new block after dash
        } elseif ($this->tokens->isNext()) {
            $item->value = $this->parseValue();
            if ($this->tokens->isNext() && !$this->tokens->isNext(\EasyCI20220216\Nette\Neon\Token::NEWLINE)) {
                $this->tokens->error();
            }
        }
        if ($item->value instanceof \EasyCI20220216\Nette\Neon\Node\BlockArrayNode) {
            $item->value->indentation = \substr($item->value->indentation, \strlen($indent));
        }
        $res->endPos = $item->endPos = $item->value->endPos;
        while ($this->tokens->consume(\EasyCI20220216\Nette\Neon\Token::NEWLINE)) {
        }
        if (!$this->tokens->isNext()) {
            return $res;
        }
        $nextIndent = $this->tokens->getIndentation();
        if (\strncmp($nextIndent, $indent, \min(\strlen($nextIndent), \strlen($indent)))) {
            $this->tokens->error('Invalid combination of tabs and spaces');
        } elseif (\strlen($nextIndent) > \strlen($indent)) {
            $this->tokens->error('Bad indentation');
        } elseif (\strlen($nextIndent) < \strlen($indent)) {
            // close block
            return $res;
        }
        goto loop;
    }
    private function parseValue() : \EasyCI20220216\Nette\Neon\Node
    {
        if ($token = $this->tokens->consume(\EasyCI20220216\Nette\Neon\Token::STRING)) {
            try {
                $node = new \EasyCI20220216\Nette\Neon\Node\StringNode(\EasyCI20220216\Nette\Neon\Node\StringNode::parse($token->value), $this->tokens->getPos() - 1);
            } catch (\EasyCI20220216\Nette\Neon\Exception $e) {
                $this->tokens->error($e->getMessage(), $this->tokens->getPos() - 1);
            }
        } elseif ($token = $this->tokens->consume(\EasyCI20220216\Nette\Neon\Token::LITERAL)) {
            $pos = $this->tokens->getPos() - 1;
            $node = new \EasyCI20220216\Nette\Neon\Node\LiteralNode(\EasyCI20220216\Nette\Neon\Node\LiteralNode::parse($token->value, $this->tokens->isNext(':', '=')), $pos);
        } elseif ($this->tokens->isNext('[', '(', '{')) {
            $node = $this->parseBraces();
        } else {
            $this->tokens->error();
        }
        return $this->parseEntity($node);
    }
    private function parseEntity(\EasyCI20220216\Nette\Neon\Node $node) : \EasyCI20220216\Nette\Neon\Node
    {
        if (!$this->tokens->isNext('(')) {
            return $node;
        }
        $attributes = $this->parseBraces();
        $entities[] = new \EasyCI20220216\Nette\Neon\Node\EntityNode($node, $attributes->items, $node->startPos, $attributes->endPos);
        while ($token = $this->tokens->consume(\EasyCI20220216\Nette\Neon\Token::LITERAL)) {
            $valueNode = new \EasyCI20220216\Nette\Neon\Node\LiteralNode(\EasyCI20220216\Nette\Neon\Node\LiteralNode::parse($token->value), $this->tokens->getPos() - 1);
            if ($this->tokens->isNext('(')) {
                $attributes = $this->parseBraces();
                $entities[] = new \EasyCI20220216\Nette\Neon\Node\EntityNode($valueNode, $attributes->items, $valueNode->startPos, $attributes->endPos);
            } else {
                $entities[] = new \EasyCI20220216\Nette\Neon\Node\EntityNode($valueNode, [], $valueNode->startPos);
                break;
            }
        }
        return \count($entities) === 1 ? $entities[0] : new \EasyCI20220216\Nette\Neon\Node\EntityChainNode($entities, $node->startPos, \end($entities)->endPos);
    }
    private function parseBraces() : \EasyCI20220216\Nette\Neon\Node\InlineArrayNode
    {
        $token = $this->tokens->consume();
        $endBrace = ['[' => ']', '{' => '}', '(' => ')'][$token->value];
        $res = new \EasyCI20220216\Nette\Neon\Node\InlineArrayNode($token->value, $this->tokens->getPos() - 1);
        $keyCheck = [];
        loop:
        while ($this->tokens->consume(\EasyCI20220216\Nette\Neon\Token::NEWLINE)) {
        }
        if ($this->tokens->consume($endBrace)) {
            $res->endPos = $this->tokens->getPos() - 1;
            return $res;
        }
        $res->items[] = $item = new \EasyCI20220216\Nette\Neon\Node\ArrayItemNode($this->tokens->getPos());
        $value = $this->parseValue();
        if ($this->tokens->consume(':', '=')) {
            $this->checkArrayKey($value, $keyCheck);
            $item->key = $value;
            $item->value = $this->tokens->isNext(\EasyCI20220216\Nette\Neon\Token::NEWLINE, ',', $endBrace) ? new \EasyCI20220216\Nette\Neon\Node\LiteralNode(null, $this->tokens->getPos()) : $this->parseValue();
        } else {
            $item->value = $value;
        }
        $item->endPos = $item->value->endPos;
        if ($this->tokens->consume(',', \EasyCI20220216\Nette\Neon\Token::NEWLINE)) {
            goto loop;
        }
        while ($this->tokens->consume(\EasyCI20220216\Nette\Neon\Token::NEWLINE)) {
        }
        if (!$this->tokens->isNext($endBrace)) {
            $this->tokens->error();
        }
        goto loop;
    }
    private function checkArrayKey(\EasyCI20220216\Nette\Neon\Node $key, array &$arr) : void
    {
        if (!$key instanceof \EasyCI20220216\Nette\Neon\Node\StringNode && !$key instanceof \EasyCI20220216\Nette\Neon\Node\LiteralNode || !\is_scalar($key->value)) {
            $this->tokens->error('Unacceptable key', $key->startPos);
        }
        $k = (string) $key->value;
        if (\array_key_exists($k, $arr)) {
            $this->tokens->error("Duplicated key '{$k}'", $key->startPos);
        }
        $arr[$k] = \true;
    }
}
