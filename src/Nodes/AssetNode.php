<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Nodes;

use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\Php\ModifierNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

class AssetNode extends StatementNode
{
    public ExpressionNode $destination;
    public ArrayNode $args;
    public ModifierNode $modifier;
    public string $mode;


    public static function create(Tag $tag): ?static
    {
        $tag->outputMode = $tag::OutputKeepIndentation;
        $tag->expectArguments();
        $node = new static();
        $node->destination = $tag->parser->parseUnquotedStringOrExpression();
        $tag->parser->stream->tryConsume(',');
        $node->args = $tag->parser->parseArguments();
        $node->modifier = $tag->parser->parseModifier();
        $node->modifier->escape = true;
        $node->modifier->check = false;
        $node->mode = $tag->name;

        if ($tag->isNAttribute()) {
            // move at the beginning
            array_unshift($tag->htmlElement->attributes->children, $node);
            return null;
        }

        return $node;
    }

    public function print(PrintContext $context): string
    {
        if ($this->mode === 'src') {
            $context->beginEscape()->enterHtmlAttribute(null, '"');
            $res = $context->format(
                <<<'XX'
                    echo ' src="'; echo %modify(\Miko\LaravelLatte\Runtime\Asset::generate(%node)); echo '"';
                    XX,
                $this->modifier,
                $this->destination,
                $this->args,
                $this->position,
            );
            $context->restoreEscape();
            return $res;
        }

        return $context->format(
            'echo %modify(\Miko\LaravelLatte\Runtime\Asset::generate(%node));',
            $this->modifier,
            $this->destination,
            $this->args,
            $this->position,
        );
    }


    public function &getIterator(): \Generator
    {
        yield $this->destination;
        yield $this->args;
        yield $this->modifier;
    }
}
