<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class ImageConverter implements NodeConverterInterface
{
    /**
     * @return ?array<DeltaOp>
     */
    public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): ?array
    {
        $src = $node->attributes->getNamedItem('src')->textContent ?? null;

        if (null === $src) {
            return null;
        }

        if (!in_array(parse_url($src, PHP_URL_SCHEME), ['', 'http', 'https'])) {
            $src = 'about:blank';
        }

        $attributes = [
            //             'alt' => $node->attributes->getNamedItem('alt')->textContent ?? null,
            'height' => $node->attributes->getNamedItem('height')->textContent ?? null,
            'width' => $node->attributes->getNamedItem('width')->textContent ?? null,
        ];

        $ops = [DeltaOp::embed('image', $src, $attributes)];

        if (null !== ($align = $node->attributes->getNamedItem('align'))) {
            $ops[] = DeltaOp::blockModifier('align', $align->textContent);
        }
        if ($node->attributes['class']) {
            if ($node->attributes['class']->nodeValue == "ql-align-center") {
                $ops[] = DeltaOp::blockModifier('align', "center");
            } elseif ($node->attributes['class']->nodeValue == "ql-align-left") {
                $ops[] = DeltaOp::blockModifier('align', "left");
            } elseif ($node->attributes['class']->nodeValue == "ql-align-right") {
                $ops[] = DeltaOp::blockModifier('align', "right");
            }
        }
        return $ops;
    }

    /**
     * @return array<string>
     */
    public function getSupportedTags(): array
    {
        return ['img'];
    }
}
