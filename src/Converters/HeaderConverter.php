<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class HeaderConverter implements NodeConverterInterface
{
    /**
     * @return array<DeltaOp>
     */
    public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): array
    {
        $modifier = DeltaOp::blockModifier('header', (int)substr($node->nodeName, 1, 1));

        if (isset($node->attributes['align'])) {
            $modifier->setAttribute('align', $node->attributes['align']->value);
        }

        return array_merge(
            [DeltaOp::text("\n")],
            $htmlConverter->convertChildren($node),
            [DeltaOp::blockModifier('header', (int)substr($node->nodeName, 1, 1))]
        );
    }

    /**
     * @return array<string>
     */
    public function getSupportedTags(): array
    {
        return ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
    }
}
