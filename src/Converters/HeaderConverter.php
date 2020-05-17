<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class HeaderConverter implements NodeConverterInterface
{

    /**
     * @param DOMNode               $element
     * @param HtmlConverterInterface $htmlConverter
     *
     * @return DeltaOp[]
     */
    public function convert(DOMNode $element, HtmlConverterInterface $htmlConverter)
    {
        $modifier = DeltaOp::blockModifier('header', (int)substr($element->nodeName, 1, 1));

        if (isset($element->attributes['align'])) {
            $modifier->setAttribute('align', $element->attributes['align']->value);
        }

        return array_merge(
            [DeltaOp::text("\n")],
            $htmlConverter->convertChildren($element),
            [DeltaOp::blockModifier('header', (int)substr($element->nodeName, 1, 1))]
        );
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
    }
}