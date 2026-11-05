<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class TextConverter implements NodeConverterInterface
{
	// Block-level tags around which whitespace-only text is not rendered.
	private const BLOCK_TAGS=['address', 'article', 'aside', 'blockquote', 'dd', 'div', 'dl', 'dt', 'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hr', 'li', 'main', 'nav', 'ol', 'p', 'pre', 'section', 'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'ul'];

	//   Preserve zero-valued text while normalizing whitespace-only nodes.
	//   Used by HtmlConverter\convertChildren.
	public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): ?DeltaOp {
		if (trim($node->textContent)==='') {
			// Drop formatting whitespace between block elements, as browsers do not render it.
			if ($this->isBlock($node->previousSibling) || $this->isBlock($node->nextSibling)) {
				return null;
			}

			// Other whitespace-only or empty text is replaced with a single space.
			return DeltaOp::text(' ');
		}

		// Retain the established removal of leading newline characters.
		return DeltaOp::text(ltrim($node->textContent, "\n"));
	}

	//   Check whether a sibling node is a block-level element.
	//   Used by TextConverter\convert.
	private function isBlock(?DOMNode $node): bool {
		// Text, comments and missing siblings are never block-level.
		return ($node!==null && in_array($node->nodeName, self::BLOCK_TAGS, true));
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