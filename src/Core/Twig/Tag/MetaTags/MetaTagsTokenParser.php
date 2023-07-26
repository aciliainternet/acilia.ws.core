<?php

namespace WS\Core\Twig\Tag\MetaTags;

use Twig\Node\Node;
use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;

class MetaTagsTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $value = $parser->getExpressionParser()->parseExpression();
        $stream->expect(Token::BLOCK_END_TYPE);

        return new MetaTagsNode('metatags_configuration', $value, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'metatags_configuration';
    }
}
