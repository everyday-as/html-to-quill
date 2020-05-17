<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class CodeConverter implements NodeConverterInterface
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

        if (1 === count($ops) && !$ops[0]->isEmbed() && false === strpos($ops[0]->getInsert(), "\n")) {
            $ops[0]->setAttribute('code', true);

            return $ops;
        }

        DeltaOp::applyAttributes($ops, ['code-block' => true]);

        return $ops;
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['code'];
    }
}