<?php

namespace WS\Core\Twig\Tag\PageConfiguration;

use Twig\Node\Node;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

class PageConfigurationNode extends Node
{
    public function __construct(string $name, AbstractExpression $value, int $lineno = 0, string $tag = null)
    {
        parent::__construct(['value' => $value], ['name' => $name], $lineno, $tag);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->raw('$this->env->getExtension(\'WS\Core\Twig\Extension\PageConfigurationExtension\')->configure(')
             ->subcompile($this->getNode('value'))
             ->raw(');');
    }
}
