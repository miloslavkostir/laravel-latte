<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Nodes;

use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

class MethodNode extends StatementNode
{
    public static bool $xhtml = false;
    private ExpressionNode $method;

    public static function create(Tag $tag): static
    {
        $tag->outputMode = $tag::OutputKeepIndentation;
        $tag->expectArguments();
        $node = $tag->node = new static();
        $node->method = $tag->parser->parseUnquotedStringOrExpression();
        return $node;
    }

    public function print(PrintContext $context): string
    {
        $xhtml = self::$xhtml ? 'true' : 'false';
        return $context->format(
            <<<XX
                echo Miko\LaravelLatte\Runtime\Method::generate(%node, $xhtml) %line;
                XX,
            $this->method,
            $this->position,
        );
    }

    public function &getIterator(): \Generator
    {
        if (false) {
            yield;
        }
    }
}
