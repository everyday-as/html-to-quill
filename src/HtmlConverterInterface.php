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
     * @return Delta The Quill version of the html
     *
     * @throws InvalidArgumentException
     */
    public function convert(string $html): Delta;

    /**
     * @return array<DeltaOp>
     */
    public function convertChildren(DOMNode $node): array;
}
