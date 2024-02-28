<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Nodes;

use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

class MethodNode extends StatementNode
{
    private ExpressionNode $method;

    public static function create(Tag $tag): ?static
    {
        $tag->outputMode = $tag::OutputKeepIndentation;
        $tag->expectArguments();
        $node = $tag->node = new self();
        $node->method = $tag->parser->parseUnquotedStringOrExpression();
        return $node;
    }

    public function print(PrintContext $context): string
    {
        return $context->format(
            <<<'XX'
                $ʟ__method = %node %line;
                Miko\LaravelLatte\Runtime\Method::validateArgument($ʟ__method);
                echo method_field($ʟ__method);
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
