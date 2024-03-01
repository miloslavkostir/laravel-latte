<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Nodes;

use Illuminate\Support\Facades\Route;
use Latte\CompileException;
use Latte\Compiler\Nodes\Php\Expression\ArrayNode;
use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\Php\ModifierNode;
use Latte\Compiler\Nodes\Php\Scalar\StringNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

class LinkNode extends StatementNode
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
        if ($this->destination instanceof StringNode && $this->destination->value === 'this' && empty($this->args->items)) {
            $expression = 'echo %modify(url()->current()) %line;';
            $params = [
                $this->modifier,
                $this->position,
            ];
        } elseif ($this->destination instanceof StringNode && $this->destination->value !== 'this') {
            list($controller, $method) = $this->findControllerAndMethod($this->destination->value);
            $expression = "echo %modify(action(['$controller', '$method'], %node)) %line;";
            $params = [
                $this->modifier,
                $this->args,
                $this->position,
            ];
        } else {
            $expression = 'echo %modify(\Miko\LaravelLatte\Runtime\Link::generate(%node, %node?)) %line;';
            $params = [
                $this->modifier,
                $this->destination,
                $this->args,
                $this->position,
            ];
        }

        if ($this->mode === 'href') {
            $context->beginEscape()->enterHtmlAttribute();
            $res = $context->format("echo ' href=\"'; ".$expression." echo '\"';", ...$params);
            $context->restoreEscape();
            return $res;
        }

        return $context->format($expression, ...$params);
    }

    public function &getIterator(): \Generator
    {
        yield $this->destination;
        yield $this->args;
        yield $this->modifier;
    }

    private function findControllerAndMethod(string $name): array
    {
        $exploded = explode('@', $name);
        if (count($exploded) === 1) {
            if (($current = Route::currentRouteAction()) === null) {
                throw new CompileException('Cannot find route action for "' . $name . '"', $this->position);
            }
            list($controller, ) = explode('@', $current);
            $method = $name;
        } else {
            $controller = class_exists('App\\Http\\Controllers\\' . $exploded[0] . 'Controller')
                ? 'App\\Http\\Controllers\\' . $exploded[0] . 'Controller'
                : 'App\\Http\\Controllers\\' . $exploded[0];
            $method = $exploded[1] ?: 'index';
        }
        return [$controller, $method];
    }
}
