<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Nodes;

use Latte\Compiler\NodeHelpers;
use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\Php\Scalar\StringNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;
use Miko\LaravelLatte\DeterministicKeys;
use Miko\LaravelLatte\Runtime\Component;

class ComponentNode extends StatementNode
{
    private ExpressionNode $name;
    private ArrayNode $args;

    public static function create(Tag $tag): static
    {
        $tag->outputMode = $tag::OutputKeepIndentation;
        $tag->expectArguments();
        $node = $tag->node = new static();
        $node->name = $tag->parser->parseUnquotedStringOrExpression();
        $tag->parser->stream->tryConsume(',');
        $node->args = $tag->parser->parseArguments();
        return $node;
    }

    public function print(PrintContext $context): string
    {
        if ($this->name instanceof StringNode) {
            // precompile
            $name = Component::composeName($this->name->value);
            $expression = "echo \Miko\LaravelLatte\Runtime\Component::generate('$name', %node) %line;";
            $params = [
                $this->args,
                $this->position,
            ];
        } else {
            $expression = "echo \Miko\LaravelLatte\Runtime\Component::generate(%node, %node) %line;";
            $params = [
                $this->name,
                $this->args,
                $this->position,
            ];
        }

        return $context->format($expression, ...$params);
    }

    public function &getIterator(): \Generator
    {
        if (false) {
            yield;
        }
    }
}
