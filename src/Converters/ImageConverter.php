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

        if (isset($node->attributes['class'])) {
            $class = $node->attributes['class'];
            $map = ['q-align-center' => 'center', 'q-align-right' => 'right', 'q-align-left' => 'left'];
            if (isset($map[$class])) {
                $ops[] = DeltaOp::blockModifier('align', $map[$class]);
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
