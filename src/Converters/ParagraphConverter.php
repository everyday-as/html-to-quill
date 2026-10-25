<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class ParagraphConverter implements NodeConverterInterface
{
	/**
	 * @return array<DeltaOp>
	 */
	//   Convert paragraph content and preserve its terminating newline.
	//   Used by HtmlConverter\convertChildren.
	public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): array {
		// A lone break is an empty paragraph placeholder, supplied by the line terminator.
		$isPlaceholderBreak=($node->childNodes->length===1 && $node->firstChild->nodeName==='br');
		$ops=($isPlaceholderBreak ? [] : $htmlConverter->convertChildren($node));
		$modifier=DeltaOp::text("\n");

		// Keep paragraph alignment attached to the terminating newline.
		if (isset($node->attributes['align'])) {
			$modifier->setAttribute('align', $node->attributes['align']->value);
		}

		// Every paragraph ends a line, including paragraphs without block attributes.
		$ops[]=$modifier;

		return $ops;
	}


	/**
	 * @return array<string>
	 */
	//   List the paragraph tags handled by this converter.
	//   Used by HtmlConverter\getConverter.
	public function getSupportedTags(): array {
		// Register the paragraph tag for converter lookup.
		return ['p'];
	}
}