<?php

namespace WS\Core\Twig\Tag\MetaTags;

use Twig\Node\Node;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

class MetaTagsNode extends Node
{
    public function __construct($name, AbstractExpression $value, $line, $tag = null)
    {
        parent::__construct(['value' => $value], ['name' => $name], $line, $tag);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->raw('$this->env->getExtension(\'WS\Core\Twig\Extension\MetadataExtension\')->register(')
             ->subcompile($this->getNode('value'))
             ->raw(');');
    }
}
