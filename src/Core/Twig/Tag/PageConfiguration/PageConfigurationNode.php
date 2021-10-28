<?php

namespace WS\Core\Twig\Tag\PageConfiguration;

use Twig\Node\Node;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

class PageConfigurationNode extends Node
{
    public function __construct($name, AbstractExpression $value, $line, $tag = null)
    {
        parent::__construct(['value' => $value], ['name' => $name], $line, $tag);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->raw('$this->env->getExtension(\'WS\Core\Twig\Extension\PageConfigurationExtension\')->configure(')
             ->subcompile($this->getNode('value'))
             ->raw(');');
    }
}
