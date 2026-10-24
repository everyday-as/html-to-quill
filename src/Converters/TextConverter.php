<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class TextConverter implements NodeConverterInterface
{
	//   Preserve zero-valued text while normalizing whitespace-only nodes.
	//   Used by HtmlConverter\convertChildren.
	public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): DeltaOp {
		// Only whitespace-only or empty text is replaced with a single space.
		if (trim($node->textContent)==='') {
			return DeltaOp::text(' ');
		}

		// Retain the established removal of leading newline characters.
		return DeltaOp::text(ltrim($node->textContent, "\n"));
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