<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Nodes;

use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

class LivewireStylesNode extends StatementNode
{
    private ArrayNode $args;

    public static function create(Tag $tag): static
    {
        $tag->outputMode = $tag::OutputKeepIndentation;
        $node = $tag->node = new static();
        $node->args = $tag->parser->parseArguments();
        return $node;
    }

    public function print(PrintContext $context): string
    {
        return $context->format(
            'echo \Miko\LaravelLatte\Runtime\Livewire::styles(%node) %line;',
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
}
