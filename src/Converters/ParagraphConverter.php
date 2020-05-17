<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class ParagraphConverter implements NodeConverterInterface
{

    /**
     * @param DOMNode               $element
     * @param HtmlConverterInterface $htmlConverter
     *
     * @return DeltaOp[]
     */
    public function convert(DOMNode $element, HtmlConverterInterface $htmlConverter)
    {
        $ops = $htmlConverter->convertChildren($element);

        $modifier = DeltaOp::text("\n");

        if (isset($element->attributes['align'])) {
            $modifier->setAttribute('align', $element->attributes['align']->value);
        }

        if ($modifier->isBlockModifier()) {
            $ops[] = $modifier;
        }

        return $ops;
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['p'];
    }
}