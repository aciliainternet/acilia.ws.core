<?php

namespace WS\Core\Twig\Tag\MetaTags;

use Twig\Attribute\YieldReady;
use Twig\Node\Node;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

#[YieldReady]
class MetaTagsNode extends Node
{
    public function __construct(string $name, AbstractExpression $value, int $lineno = 0)
    {
        parent::__construct(['value' => $value], ['name' => $name], $lineno);
    }

    #[\Override]
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->raw('$this->env->getExtension(\'WS\Core\Twig\Extension\MetadataExtension\')->register(')
             ->subcompile($this->getNode('value'))
             ->raw(');');
    }
}
