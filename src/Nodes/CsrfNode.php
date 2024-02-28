<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Nodes;

use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

class CsrfNode extends StatementNode
{
    public static function create(Tag $tag): ?static
    {
        $node = $tag->node = new self();
        return $node;
    }

    public function print(PrintContext $context): string
    {
        return $context->format(
            <<<'XX'
                echo \Miko\LaravelLatte\Runtime\Csrf::generate() %line;
                XX,
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
