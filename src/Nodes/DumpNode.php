<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Nodes;

use Latte\Compiler\PrintContext;

class DumpNode extends \Latte\Essential\Nodes\DumpNode
{
    public function print(PrintContext $context): string
    {
        if (class_exists('Tracy\Debugger') && \Tracy\Debugger::isEnabled()) {
            return parent::print($context);
        }
        return $this->expression
            ? $context->format(
                'Symfony\\Component\\VarDumper\\VarDumper::dump(%node, %dump) %line;',
                $this->expression,
                $this->expression->print($context),
                $this->position,
            )
            : $context->format(
                "Symfony\\Component\\VarDumper\\VarDumper::dump(get_defined_vars(), 'variables') %line;",
                $this->position,
            );
    }
}