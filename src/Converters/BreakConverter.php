<?php

namespace Everyday\HtmlToQuill\Converters;

use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class BreakConverter implements NodeConverterInterface
{

    /**
     * @param \DOMNode               $element
     * @param HtmlConverterInterface $htmlConverter
     *
     * @return DeltaOp
     */
    public function convert(\DOMNode $element, HtmlConverterInterface $htmlConverter)
    {
        return DeltaOp::text("\n");
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['br'];
    }
}