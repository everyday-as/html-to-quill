<?php

namespace Everyday\HtmlToQuill\Converters;

use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class LinkConverter implements NodeConverterInterface
{
    /**
     * @return ?array<DeltaOp>
     */
    public function convert(\DOMNode $node, HtmlConverterInterface $htmlConverter): ?array
    {
        if (!$node->hasChildNodes() || empty($ops = $htmlConverter->convertChildren($node))) {
            return null;
        }

        if (null !== ($href = $node->attributes->getNamedItem('href'))) {
            $href = $href->textContent;

            if (!in_array(parse_url($href, PHP_URL_SCHEME), ['', 'http', 'https'])) {
                $href = 'about:blank';
            }

            DeltaOp::applyAttributes($ops, ['link' => $href]);

            if (null !== ($target = $node->attributes->getNamedItem('target'))) {
                DeltaOp::applyAttributes($ops, ['target' => $target->textContent]);
            }
        }

        return $ops;
    }

    /**
     * @return array<string>
     */
    public function getSupportedTags(): array
    {
        return ['a'];
    }
}
