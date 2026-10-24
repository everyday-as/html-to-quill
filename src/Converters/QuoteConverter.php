<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class QuoteConverter implements NodeConverterInterface
{
	/**
	 * @return array<DeltaOp>
	 */
	//   Attach blockquote formatting to the terminator of each quoted line.
	//   Used by HtmlConverter\convertChildren.
	public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): array {
		// Convert the quoted content before separating its line terminators.
		$ops=$htmlConverter->convertChildren($node);
		$quoteOps=[];
		foreach ($ops as $op) {
			// Preserve embedded content without treating it as a text string.
			if ($op->isEmbed()) {
				$quoteOps[]=$op;
				continue;
			}

			// Retain text formatting and any existing attributes on line terminators.
			$attributes=$op->getAttributes();
			$lineAttributes=($op->isBlockModifier() ? $attributes : []);
			$lineAttributes['blockquote']=true;
			$parts=explode("\n", $op->getInsert());
			$lastPart=count($parts)-1;
			foreach ($parts as $index => $part) {
				// Keep inline attributes on text rather than adding the quote format to it.
				if ($part!=='') {
					$quoteOps[]=DeltaOp::text($part, $attributes);
				}

				// Mark each existing line break as a quoted line terminator.
				if ($index<$lastPart) {
					$quoteOps[]=DeltaOp::text("\n", $lineAttributes);
				}
			}
		}

		// Terminate the final quoted line, including an empty blockquote.
		$lastOp=end($quoteOps);
		if ($lastOp===false || $lastOp->getInsert()!=="\n") {
			$quoteOps[]=DeltaOp::blockModifier('blockquote');
		}

		return $quoteOps;
	}


	/**
	 * @return array<string>
	 */
	//   List the quote tags handled by this converter.
	//   Used by HtmlConverter\getConverter.
	public function getSupportedTags(): array {
		// Register the blockquote tag for converter lookup.
		return ['blockquote'];
	}
}