<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Nodes;

use Latte\Compiler\NodeHelpers;
use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\Php\ModifierNode;
use Latte\Compiler\Nodes\Php\Scalar\StringNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;
use Miko\LaravelLatte\Runtime\Translator;

class TranslationNode extends StatementNode
{
    public static $autoRefresh = false;
    private ExpressionNode $key;
    private ArrayNode $args;
    private ModifierNode $modifier;

    public static function create(Tag $tag): ?static
    {
        $tag->outputMode = $tag::OutputKeepIndentation;
        $tag->expectArguments();
        $node = new static();
        $node->key = $tag->parser->parseUnquotedStringOrExpression();
        if ($tag->parser->stream->tryConsume(',')) {
            $node->args = $tag->parser->parseArguments();
        } else {
            $node->args = new ArrayNode();
        }
        $node->modifier = $tag->parser->parseModifier();
        $node->modifier->escape = true;
        return $node;
    }

    public function print(PrintContext $context): string
    {
        $args = $this->toValue($this->args);
        // $args === null if arguments contains a variable
        // $args === [] if there are no arguments
        if (!self::$autoRefresh && $this->key instanceof StringNode && $args !== null) {
            $translated = Translator::translate($this->key->value, ...$args);
            return $context->format(
                "echo %modify('$translated') %line;",
                $this->modifier,
                $this->position
            );
        }
        return $context->format(
            'echo %modify(\Miko\LaravelLatte\Runtime\Translator::translate(%node, ...%node)) %line;',
            $this->modifier,
            $this->key,
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

    private function toValue($args): mixed
    {
        try {
            return NodeHelpers::toValue($args, constants: true);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }
}
