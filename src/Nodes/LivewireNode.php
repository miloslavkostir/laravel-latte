<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Nodes;

use Latte\Compiler\NodeHelpers;
use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;
use Miko\LaravelLatte\DeterministicKeys;

class LivewireNode extends StatementNode
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
        $key = $this->findKey();
        return $context->format(
            "echo \Miko\LaravelLatte\Runtime\Livewire::generate(%node, %node, '$key') %line;",
            $this->name,
            $this->args,
            $this->position
        );
    }

    public function &getIterator(): \Generator
    {
        if (false) {
            yield;
        }
    }

    private function findKey(): string
    {
        foreach ($this->args as $index => $value) {
            // named argument has 'name' {livewire ... key: foo}
            // array index has 'value' {livewire ... key => foo}
            if ('key' === ($value->key->name ?? $value->key->value ?? false)) {
                unset($this->args->items[$index]);
                return (string) self::toValue($value->value);
            }
        }
        return DeterministicKeys::generate('lw');
    }

    public static function toValue(ExpressionNode $node): mixed
    {
        try {
            return NodeHelpers::toValue($node, constants: true);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }
}
