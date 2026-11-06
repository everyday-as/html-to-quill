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
     * Whether the content before the header already ends with a newline.
     */
    private function followsLineEnding(DOMNode $node, HtmlConverterInterface $htmlConverter): bool
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

        if (null === $sibling) {
            return true;
        }

        // Convert a copy of the preceding content, so custom converters and wrapped blocks are checked as they convert
        $wrapper = $sibling->ownerDocument->createElement('div');
        $wrapper->appendChild($sibling->cloneNode(true));
        $ops = $htmlConverter->convertChildren($wrapper);
        $last = end($ops);

        return false !== $last && !$last->isEmbed() && str_ends_with($last->getInsert(), "\n");
    }
}
