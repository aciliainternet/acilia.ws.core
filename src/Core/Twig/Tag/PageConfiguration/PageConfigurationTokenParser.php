<?php

namespace WS\Core\Twig\Tag\PageConfiguration;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class PageConfigurationTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): PageConfigurationNode
    {
        $parser = $this->parser;
        $parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new PageConfigurationNode('page_configuration', $parser->parseExpression(), $token->getLine());
    }

    public function getTag(): string
    {
        return 'page_configuration';
    }
}
