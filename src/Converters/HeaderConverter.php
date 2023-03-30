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

        if ($node->attributes['class']) {
            if ($node->attributes['class']->nodeValue == "ql-align-center") {
                $modifier->setAttribute('align', 'center');
            } elseif ($node->attributes['class']->nodeValue == "ql-align-left") {
                $modifier->setAttribute('align', 'left');
            } elseif ($node->attributes['class']->nodeValue == "ql-align-right") {
                $modifier->setAttribute('align', 'right');
            }
        }
        
        $ops = [];
        if ($modifier->isBlockModifier()) {
            $ops[] = $modifier;
        }
        return array_merge(
            [DeltaOp::text("\n")],
            $htmlConverter->convertChildren($node),
            [DeltaOp::blockModifier('header', (int)substr($node->nodeName, 1, 1))],
            $ops,
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
