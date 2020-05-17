<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class EmphasisConverter implements NodeConverterInterface
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

        switch ($element->nodeName) {
            case 'b':
                DeltaOp::applyAttributes($ops, ['bold' => true]);
                break;
            case 'em':
            case 'i':
                DeltaOp::applyAttributes($ops, ['italic' => true]);
                break;
        }

        return $ops;
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['b', 'em', 'i'];
    }
}