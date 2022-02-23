<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class TextConverter implements NodeConverterInterface
{
    public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): DeltaOp
    {
        if (empty(trim($node->textContent))) {
            return DeltaOp::text(' ');
        }

        return DeltaOp::text(ltrim($node->textContent, "\n"));
    }

    /**
     * @return array<string>
     */
    public function getSupportedTags(): array
    {
        return ['#text'];
    }
}
