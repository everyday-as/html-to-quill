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
     * Tags whose converted content already ends with a newline.
     */
    private const LINE_ENDING_TAGS = ['br', 'p', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

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
            $this->followsLineEnding($node) ? [] : [DeltaOp::text("\n")],
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
     * Whether the content before the header already ends with a newline.
     */
    private function followsLineEnding(DOMNode $node): bool
    {
        $sibling = $node->previousSibling;

        // Skip comments and whitespace, and continue before the parent when the header is its first child
        while (true) {
            if ($sibling instanceof DOMComment || ($sibling instanceof DOMText && trim($sibling->textContent) === '')) {
                $sibling = $sibling->previousSibling;
            } elseif (null === $sibling && null !== $node->parentNode && 'body' !== $node->parentNode->nodeName) {
                $node = $node->parentNode;
                $sibling = $node->previousSibling;
            } else {
                break;
            }
        }

        return null === $sibling || in_array($sibling->nodeName, self::LINE_ENDING_TAGS, true);
    }
}
