<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class TextConverter implements NodeConverterInterface
{

    /**
     * @param DOMNode               $element
     * @param HtmlConverterInterface $htmlConverter
     *
     * @return DeltaOp
     */
    public function convert(DOMNode $element, HtmlConverterInterface $htmlConverter)
    {
        if (empty(trim($element->textContent))) {
            return DeltaOp::text(' ');
        }

        return DeltaOp::text(ltrim($element->textContent, "\n"));
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['#text'];
    }
}