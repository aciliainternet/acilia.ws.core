<?php

namespace WS\Core\Twig\Tag\PageConfiguration;

use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

#[YieldReady]
class PageConfigurationNode extends Node
{
    public function __construct(string $name, AbstractExpression $value, int $lineno = 0)
    {
        parent::__construct(['value' => $value], ['name' => $name], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->raw('$this->env->getExtension(\'WS\Core\Twig\Extension\PageConfigurationExtension\')->configure(')
             ->subcompile($this->getNode('value'))
             ->raw(');');
    }
}
