<?php

use Everyday\HtmlToQuill\HtmlConverter;

// Load this checkout with Composer dependencies, optionally supplied by the caller.
$loader=require ($argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php');
$loader->addPsr4('Everyday\\HtmlToQuill\\', dirname(__DIR__) . '/src/', true);

// Preserve separate paragraphs, including an empty line between nonempty paragraphs.
$cases=[
	'adjacent paragraphs'    => ['<p>First</p><p>Second</p>', "First\nSecond\n"],
	'empty middle paragraph' => ['<p>First</p><p></p><p>Third</p>', "First\n\nThird\n"],
	'formatted paragraph'    => ['<p><b>First</b></p><p>Second</p>', "First\nSecond\n"],
	'aligned paragraph'      => ['<p align="center">First</p><p>Second</p>', "First\nSecond\n"]
];
$converter=new HtmlConverter();
foreach ($cases as $label => [$html, $expected]) {
	// Verify visible line boundaries after the actual conversion and compaction.
	$delta=$converter->convert($html);
	if ($delta->toPlainText()!==$expected) {
		throw new RuntimeException($label . ': ' . $delta->toJson());
	}
}

// Keep paragraph alignment on its own newline rather than applying it to later text.
$ops=$converter->convert('<p align="center">First</p><p>Second</p>')->getOps();
$alignedLines=0;
foreach ($ops as $op) {
	// Check both the alignment value and the operation carrying it.
	if ($op->hasAttribute('align')) {
		if ($op->getInsert()!=="\n" || $op->getAttribute('align')!=='center') {
			throw new RuntimeException('Paragraph alignment moved off its newline.');
		}
		$alignedLines++;
	}
}
if ($alignedLines!==1) {
	throw new RuntimeException('Expected one aligned paragraph.');
}
echo "Paragraph boundary regression checks passed.\n";