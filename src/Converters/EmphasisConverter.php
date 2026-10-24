<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class EmphasisConverter implements NodeConverterInterface {

	/**
	 * @return array<DeltaOp>
	 */
	//   Convert inline emphasis tags to Quill attributes.
	//   Used by HtmlConverter\convertChildren.
	public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): array {
		// Convert child nodes before applying the enclosing emphasis.
		$ops=$htmlConverter->convertChildren($node);

		// Apply the Quill attribute matching the HTML tag.
		match ($node->nodeName) {
			'b', 'strong' => DeltaOp::applyAttributes($ops, ['bold' => true]),
			'em', 'i' => DeltaOp::applyAttributes($ops, ['italic' => true]),
			'u' => DeltaOp::applyAttributes($ops, ['underline' => true]),
			's', 'strike', 'del' => DeltaOp::applyAttributes($ops, ['strike' => true]),
		};

		return $ops;
	}


	/**
	 * @return array<string>
	 */
	//   List the HTML emphasis tags handled by this converter.
	//   Used by HtmlConverter\getConverter.
	public function getSupportedTags(): array {
		// Register all supported emphasis tags for converter lookup.
		return ['b', 'strong', 'em', 'i', 'u', 's', 'strike', 'del'];
	}
}