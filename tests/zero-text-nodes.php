<?php

use Everyday\HtmlToQuill\Converters\TextConverter;
use Everyday\HtmlToQuill\HtmlConverter;

// Load this checkout with Composer dependencies, optionally supplied by the caller.
$loader=require ($argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php');
$loader->addPsr4('Everyday\\HtmlToQuill\\', dirname(__DIR__) . '/src/', true);

// Check the converter independently of downstream Delta compaction.
$cases=[
	'zero'             => ['0', '0'],
	'zero with spaces' => [' 0 ', ' 0 '],
	'ordinary text'    => ['text', 'text'],
	'empty text'       => ['', ' '],
	'whitespace only'  => [" \n\t ", ' '],
	'leading newline'  => ["\n0", '0']
];
$converter=new TextConverter();
$htmlConverter=new HtmlConverter();
foreach ($cases as $label => [$text, $expected]) {
	// Preserve zero without changing the established whitespace conversion.
	$op=$converter->convert(new DOMText($text), $htmlConverter);
	if ($op->getInsert()!==$expected) {
		throw new RuntimeException($label . ': ' . $op->toJson());
	}
}
echo "Zero text node regression checks passed.\n";