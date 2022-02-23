<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class QuoteConverter implements NodeConverterInterface
{
    /**
     * @return array<DeltaOp>
     */
    public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): array
    {
        $ops = $htmlConverter->convertChildren($node);

        if (1 === count($ops) && !$ops[0]->isEmbed() && !str_contains($ops[0]->getInsert(), "\n")) {
            $ops[0]->setAttribute('blockquote', true);

            return $ops;
        }

        DeltaOp::applyAttributes($ops, ['blockquote' => true]);

        return $ops;
    }

    /**
     * @return array<string>
     */
    public function getSupportedTags(): array
    {
        return ['blockquote'];
    }
}
