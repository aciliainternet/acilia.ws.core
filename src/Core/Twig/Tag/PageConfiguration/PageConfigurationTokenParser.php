<?php

namespace WS\Core\Twig\Tag\PageConfiguration;

use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;

class PageConfigurationTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): PageConfigurationNode
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $value = $parser->getExpressionParser()->parseExpression();
        $stream->expect(Token::BLOCK_END_TYPE);
        $name = 'page_configuration';

        return new PageConfigurationNode($name, $value, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'page_configuration';
    }
}
