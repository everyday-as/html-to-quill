<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class ParagraphConverter implements NodeConverterInterface
{
    /**
     * @return array<DeltaOp>
     */
    public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): array
    {
        $ops = $htmlConverter->convertChildren($node);

        $modifier = DeltaOp::text("\n");

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
        if ($modifier->isBlockModifier()) {
            $ops[] = $modifier;
        }

        return $ops;
    }

    /**
     * @return array<string>
     */
    public function getSupportedTags(): array
    {
        return ['p'];
    }
}
