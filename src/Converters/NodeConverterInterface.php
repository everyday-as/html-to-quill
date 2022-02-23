<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

interface NodeConverterInterface
{
    /**
     * @return array<DeltaOp>|DeltaOp|null
     */
    public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): DeltaOp|array|null;

    /**
     * @return array<string>
     */
    public function getSupportedTags(): array;
}
