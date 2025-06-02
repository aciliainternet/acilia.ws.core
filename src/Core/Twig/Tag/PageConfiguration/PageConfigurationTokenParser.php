<?php

namespace WS\Core\Twig\Tag\PageConfiguration;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class PageConfigurationTokenParser extends AbstractTokenParser
{
    #[\Override]
    public function parse(Token $token): PageConfigurationNode
    {
        $value = $this->parser->parseExpression();
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new PageConfigurationNode('page_configuration', $value, $token->getLine());
    }

    #[\Override]
    public function getTag(): string
    {
        return 'page_configuration';
    }
}
