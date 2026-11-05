<?php

use Everyday\HtmlToQuill\HtmlConverter;

// Load this checkout with Composer dependencies, optionally supplied by the caller.
$loader=require ($argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php');
$loader->addPsr4('Everyday\\HtmlToQuill\\', dirname(__DIR__) . '/src/', true);

// Header alignment belongs to the same terminating newline as the header level.
$cases=[
	'aligned header'   => ['<h2 align="center">Title</h2>', 2, 'center'],
	'unaligned header' => ['<h1>Title</h1>', 1, null]
];
$converter=new HtmlConverter();
foreach ($cases as $label => [$html, $expectedLevel, $expectedAlign]) {
	// Verify the header text, then the attributes of its line terminator.
	$delta=$converter->convert($html);
	if ($delta->toPlainText()!=="Title\n") {
		throw new RuntimeException($label . ': ' . $delta->toJson());
	}
	$ops=$delta->getOps();
	$terminator=end($ops);
	if ($terminator->getAttribute('header')!==$expectedLevel) {
		throw new RuntimeException($label . ': header level was lost.');
	}
	if ($expectedAlign===null ? $terminator->hasAttribute('align') : $terminator->getAttribute('align')!==$expectedAlign) {
		throw new RuntimeException($label . ': incorrect header alignment: ' . $delta->toJson());
	}
}
echo "Header alignment regression checks passed.\n";