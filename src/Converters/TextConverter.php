<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMComment;
use DOMNode;
use DOMText;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class TextConverter implements NodeConverterInterface
{
	//   Preserve zero-valued text while normalizing whitespace-only nodes.
	//   Used by HtmlConverter\convertChildren.
	public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): ?DeltaOp {
		if (trim($node->textContent)==='') {
			// Drop formatting whitespace after content that already ends a line, as browsers do not render it.
			if ($this->followsLineEnding($node, $htmlConverter)) {
				return null;
			}

			// Other whitespace-only or empty text is replaced with a single space.
			return DeltaOp::text(' ');
		}

		// Retain the established removal of leading newline characters.
		return DeltaOp::text(ltrim($node->textContent, "\n"));
	}

	//   Check whether the content before the node, skipping comments, whitespace and empty content, already ends a line.
	//   Used by TextConverter\convert, TextConverter\followsLineEnding.
	private function followsLineEnding(DOMNode $node, HtmlConverterInterface $htmlConverter): bool {
		// Skip comments and whitespace, and continue before the parent when the node comes first.
		$previous=$node->previousSibling;
		if ($previous instanceof DOMComment || ($previous instanceof DOMText && trim($previous->textContent)==='')) {
			return $this->followsLineEnding($previous, $htmlConverter);
		}
		if ($previous===null) {
			return ($node->parentNode!==null && $node->parentNode->nodeName!=='body' && $this->followsLineEnding($node->parentNode, $htmlConverter));
		}

		// Convert a copy of the preceding content and check whether it ends with a newline.
		$wrapper=$previous->ownerDocument->createElement('div');
		$wrapper->appendChild($previous->cloneNode(true));
		$ops=$htmlConverter->convertChildren($wrapper);

		// Skip content that converts to nothing, such as an empty element.
		if ($ops===[]) {
			return $this->followsLineEnding($previous, $htmlConverter);
		}

		return (end($ops) instanceof DeltaOp && !end($ops)->isEmbed() && str_ends_with(end($ops)->getInsert(), "\n"));
	}


	/**
	 * @return array<string>
	 */
	//   List the text nodes handled by this converter.
	//   Used by HtmlConverter\getConverter.
	public function getSupportedTags(): array {
		// Register text nodes for converter lookup.
		return ['#text'];
	}
}