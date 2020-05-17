<?php

namespace Everyday\HtmlToQuill;

use DOMNode;
use Everyday\QuillDelta\Delta;
use Everyday\QuillDelta\DeltaOp;
use InvalidArgumentException;

interface HtmlConverterInterface
{
    /**
     * Convert the given $html to Markdown
     *
     * @param string $html
     *
     * @return Delta The Markdown version of the html
     * @throws InvalidArgumentException
     *
     */
    public function convert($html): Delta;

    /**
     * @param DOMNode $node
     *
     * @return DeltaOp[]
     */
    public function convertChildren(DOMNode $node): array;
}
