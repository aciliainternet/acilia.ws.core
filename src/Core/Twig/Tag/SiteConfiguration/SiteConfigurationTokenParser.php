<?php

namespace WS\Core\Twig\Tag\SiteConfiguration;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class SiteConfigurationTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): SiteConfigurationNode
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $value = $parser->getExpressionParser()->parseExpression();
        $stream->expect(Token::BLOCK_END_TYPE);
        $name = 'site_configuration';

        return new SiteConfigurationNode($name, $value, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'site_configuration';
    }
}
