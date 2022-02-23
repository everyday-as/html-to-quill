<?php

namespace Everyday\HtmlToQuill\Converters;

use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class BreakConverter implements NodeConverterInterface
{
    public function convert(\DOMNode $node, HtmlConverterInterface $htmlConverter): DeltaOp
    {
        return DeltaOp::text("\n");
    }

    /**
     * @return array<string>
     */
    public function getSupportedTags(): array
    {
        return ['br'];
    }
}
