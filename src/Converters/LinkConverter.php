<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class LinkConverter implements NodeConverterInterface
{

    /**
     * @param DOMNode               $element
     * @param HtmlConverterInterface $htmlConverter
     *
     * @return DeltaOp[]
     */
    public function convert(DOMNode $element, HtmlConverterInterface $htmlConverter)
    {
        if (!$element->hasChildNodes() || empty($ops = $htmlConverter->convertChildren($element))) {
            return null;
        }

        if (null !== ($href = $element->attributes->getNamedItem('href'))) {
            $href = $href->textContent;

            if (!in_array(parse_url($href, PHP_URL_SCHEME), ['', 'http', 'https'])) {
                $href = 'about:blank';
            }

            DeltaOp::applyAttributes($ops, ['link' => $href]);

            if (null !== ($target = $element->attributes->getNamedItem('target'))) {
                DeltaOp::applyAttributes($ops, ['target' => $target->textContent]);
            }
        }

        return $ops;
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['a'];
    }
}