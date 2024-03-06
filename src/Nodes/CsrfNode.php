<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Nodes;

use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

class CsrfNode extends StatementNode
{
    public static bool $xhtml = false;

    public static function create(Tag $tag): static
    {
        $node = $tag->node = new static();
        return $node;
    }

    public function print(PrintContext $context): string
    {
        $xhtml = self::$xhtml ? 'true' : 'false';
        return $context->format(
            <<<XX
                echo \Miko\LaravelLatte\Runtime\Csrf::generate($xhtml) %line;
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
