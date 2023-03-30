<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class EmphasisConverter implements NodeConverterInterface
{
    /**
     * @return array<DeltaOp>
     */
    public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): array
    {
        $ops = $htmlConverter->convertChildren($node);

        match ($node->nodeName) {
            'b', 'strong' => DeltaOp::applyAttributes($ops, ['bold' => true]),
            'em', 'i' => DeltaOp::applyAttributes($ops, ['italic' => true]),
            'u' => DeltaOp::applyAttributes($ops, ['underline' => true]),
        };

        if ($node->parentNode->nodeName !== "li" && !$node->hasChildNodes()) {
            return array_merge(
                [DeltaOp::text("\n")],
                $ops,
            );
        }
        return $ops;
    }

    /**
     * @return array<string>
     */
    public function getSupportedTags(): array
    {
        return ['strong', 'b', 'em', 'i', 'u'];
    }
}
