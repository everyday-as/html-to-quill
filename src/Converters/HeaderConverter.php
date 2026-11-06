<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMComment;
use DOMNode;
use DOMText;
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
            $this->followsLineEnding($node, $htmlConverter) ? [] : [DeltaOp::text("\n")],
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

    /**
     * Whether the content before the node, skipping comments and whitespace, already ends with a newline.
     */
    private function followsLineEnding(DOMNode $node, HtmlConverterInterface $htmlConverter): bool
    {
        // Skip comments and whitespace, and continue before the parent when the node comes first
        $previous = $node->previousSibling;
        if ($previous instanceof DOMComment || ($previous instanceof DOMText && trim($previous->textContent) === '')) {
            return $this->followsLineEnding($previous, $htmlConverter);
        }
        if (null === $previous) {
            return null === $node->parentNode || 'body' === $node->parentNode->nodeName || $this->followsLineEnding($node->parentNode, $htmlConverter);
        }

        // Convert a copy of the preceding content, so custom converters and wrapped blocks are checked as they convert
        $wrapper = $previous->ownerDocument->createElement('div');
        $wrapper->appendChild($previous->cloneNode(true));
        $ops = $htmlConverter->convertChildren($wrapper);

        return end($ops) instanceof DeltaOp && !end($ops)->isEmbed() && str_ends_with(end($ops)->getInsert(), "\n");
    }
}
