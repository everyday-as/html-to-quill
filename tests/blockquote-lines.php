<?php

use Everyday\HtmlToQuill\HtmlConverter;

// Load this checkout with Composer dependencies, optionally supplied by the caller.
$loader=require ($argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php');
$loader->addPsr4('Everyday\\HtmlToQuill\\', dirname(__DIR__) . '/src/', true);

// Blockquote formatting belongs to each terminating newline, including empty quoted lines.
$cases=[
	'plain quote'       => ['<blockquote>Quote</blockquote>', "Quote\n", 1],
	'formatted quote'   => ['<blockquote><b>a</b>b</blockquote>', "ab\n", 1],
	'multiline quote'   => ['<blockquote>a<br />b</blockquote>', "a\nb\n", 2],
	'empty quote'       => ['<blockquote></blockquote>', "\n", 1],
	'empty middle line' => ['<blockquote>a<br /><br />b</blockquote>', "a\n\nb\n", 3],
	'terminated quote'  => ['<blockquote>a<br /></blockquote>', "a\n", 1],
	'following text'    => ['<blockquote>Quote</blockquote>After', "Quote\nAfter\n", 1],
	'aligned quote'     => ['<blockquote><p align="center">Quote</p></blockquote>', "Quote\n", 1]
];
$converter=new HtmlConverter();
foreach ($cases as $label => [$html, $expectedText, $expectedLines]) {
	// Verify actual conversion, then count the quoted newline operations.
	$delta=$converter->convert($html);
	if ($delta->toPlainText()!==$expectedText) {
		throw new RuntimeException($label . ': ' . $delta->toJson());
	}
	$quotedLines=0;
	foreach ($delta->getOps() as $op) {
		if ($op->hasAttribute('blockquote')) {
			$insert=$op->getInsert();
			if (!is_string($insert) || trim($insert, "\n")!=='' || $op->getAttribute('blockquote')!==true) {
				throw new RuntimeException($label . ': blockquote attribute is not on a newline.');
			}
			$quotedLines+=substr_count($insert, "\n");
		}
	}
	if ($quotedLines!==$expectedLines) {
		throw new RuntimeException($label . ': incorrect quoted line count.');
	}
}

// Preserve both inline content attributes and existing paragraph alignment.
$ops=$converter->convert('<blockquote><b>a</b>b</blockquote>')->getOps();
if ($ops[0]->getAttribute('bold')!==true) {
	throw new RuntimeException('Nested bold formatting was lost.');
}
$ops=$converter->convert('<blockquote><p align="center">Quote</p></blockquote>')->getOps();
if (end($ops)->getAttribute('align')!=='center') {
	throw new RuntimeException('Quote alignment was lost.');
}
echo "Blockquote line regression checks passed.\n";