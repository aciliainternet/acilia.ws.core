<?php

namespace WS\Core\Twig\Tag\SiteConfiguration;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class SiteConfigurationTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): SiteConfigurationNode
    {
        $parser = $this->parser;
        $parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new SiteConfigurationNode('site_configuration', $parser->parseExpression(), $token->getLine());
    }

    public function getTag(): string
    {
        return 'site_configuration';
    }
}
