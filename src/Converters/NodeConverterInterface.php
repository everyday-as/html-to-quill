<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

interface NodeConverterInterface
{
    /**
     * @param DOMNode               $node
     * @param HtmlConverterInterface $htmlConverter
     *
     * @return DeltaOp[]|DeltaOp|null
     */
    public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter);

    /**
     * @return array
     */
    public function getSupportedTags(): array;
}