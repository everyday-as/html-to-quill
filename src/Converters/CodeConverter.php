<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class CodeConverter implements NodeConverterInterface
{
	/**
	 * @return array<DeltaOp>
	 */
	//   Preserve inline code across multiple child operations.
	//   Used by HtmlConverter\convertChildren.
	public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): array {
		// Convert the children while retaining their existing inline formatting.
		$ops=$htmlConverter->convertChildren($node);
		$isInline=true;

		// Keep the block treatment for embedded or multiline content.
		foreach ($ops as $op) {
			if ($op->isEmbed() || str_contains($op->getInsert(), "\n")) {
				$isInline=false;
				break;
			}
		}

		// Apply one code format to the complete span, regardless of operation count.
		$attribute=($isInline ? 'code' : 'code-block');
		DeltaOp::applyAttributes($ops, [$attribute => true]);

		return $ops;
	}


	/**
	 * @return array<string>
	 */
	//   List the code tags handled by this converter.
	//   Used by HtmlConverter\getConverter.
	public function getSupportedTags(): array {
		// Register the code tag for converter lookup.
		return ['code'];
	}
}