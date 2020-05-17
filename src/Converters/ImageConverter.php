<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class ImageConverter implements NodeConverterInterface
{

    /**
     * @param DOMNode               $element
     * @param HtmlConverterInterface $htmlConverter
     *
     * @return DeltaOp[]
     */
    public function convert(DOMNode $element, HtmlConverterInterface $htmlConverter)
    {
        $src = $element->attributes->getNamedItem('src')->textContent ?? null;

        if (null === $src)
        {
            return null;
        }

        if (!in_array(parse_url($src, PHP_URL_SCHEME), ['', 'http', 'https'])) {
            $src = 'about:blank';
        }

        $attributes = [
            'alt' => $element->attributes->getNamedItem('alt')->textContent ?? null,
            'height' => $element->attributes->getNamedItem('height')->textContent ?? null,
            'width' => $element->attributes->getNamedItem('width')->textContent ?? null,
        ];

        $ops = [DeltaOp::embed('image', $src, $attributes)];

        if (null !== ($align = $element->attributes->getNamedItem('align'))) {
            $ops[] = DeltaOp::blockModifier('align', $align->textContent);
        }

        return $ops;
    }

    /**
     * @return string[]
     */
    public function getSupportedTags(): array
    {
        return ['img'];
    }
}