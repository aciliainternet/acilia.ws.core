<?php

namespace WS\Core\Twig\Tag\MetaTags;

use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;

class MetaTagsTokenParser extends AbstractTokenParser
{
    #[\Override]
    public function parse(Token $token): MetaTagsNode
    {
        $value = $this->parser->parseExpression();
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new MetaTagsNode('metatags_configuration', $value, $token->getLine());
    }

    #[\Override]
    public function getTag(): string
    {
        return 'metatags_configuration';
    }
}
