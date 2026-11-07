<?php

use Everyday\HtmlToQuill\HtmlConverter;

// Load this checkout with Composer dependencies, optionally supplied by the caller.
$loader=require ($argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php');
$loader->addPsr4('Everyday\\HtmlToQuill\\', dirname(__DIR__) . '/src/', true);

// Formatting whitespace between block elements is dropped, while whitespace between inline elements remains a space.
$cases=[
	'paragraphs on separate lines' => ["<p>a</p>\n<p>b</p>", "a\nb\n"],
	'indented nested paragraphs'   => ["<div>\n  <p>a</p>\n  <p>b</p>\n</div>", "a\nb\n"],
	'quote before paragraph'       => ["<blockquote>a</blockquote>\n<p>b</p>", "a\nb\n"],
	'inline after paragraph'       => ['<p>a</p> <b>b</b>', "a\nb\n"],
	'space between inline tags'    => ['<p><b>a</b> <i>b</i></p>', "a b\n"],
	'newline between inline tags'  => ["<p><b>a</b>\n<i>b</i></p>", "a b\n"],
	'blocks without line endings'  => ['<div>one</div> <div>two</div>', "one two\n"],
	'comments between blocks'      => ["<p>a</p><!--x-->\n<!--y--><p>b</p>", "a\nb\n"],
	'empty element between blocks' => ["<p>a</p><span></span>\n<p>b</p>", "a\nb\n"],
	'empty elements and newlines'  => ["<p>a</p>\n<b></b><i></i>\n<p>b</p>", "a\nb\n"],
	'inline after empty element'   => ['<b>a</b><span></span> <i>b</i>', "a b\n"]
];
$converter=new HtmlConverter();
foreach ($cases as $label => [$html, $expectedText]) {
	// Verify the text, so stray spaces or space-only lines fail the check.
	$delta=$converter->convert($html);
	if ($delta->toPlainText()!==$expectedText) {
		throw new RuntimeException($label . ': ' . $delta->toJson());
	}
}
echo "Block whitespace regression checks passed.\n";