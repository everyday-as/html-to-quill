<?php

use Everyday\HtmlToQuill\Converters\NodeConverterInterface;
use Everyday\HtmlToQuill\HtmlConverter;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

// Load this checkout with Composer dependencies, optionally supplied by the caller.
$loader=require ($argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php');
$loader->addPsr4('Everyday\\HtmlToQuill\\', dirname(__DIR__) . '/src/', true);

// A header starts a new line only when the preceding content has not already ended one.
$cases=[
	'consecutive headers'     => ['<h1>a</h1><h2>b</h2>', "a\nb\n"],
	'paragraph before header' => ['<p>a</p><h1>b</h1>', "a\nb\n"],
	'quote before header'     => ['<blockquote>a</blockquote><h1>b</h1>', "a\nb\n"],
	'comment before header'   => ['<p>a</p><!-- note --><h1>b</h1>', "a\nb\n"],
	'nested after paragraph'  => ['<div><p>a</p><h1>b</h1></div>', "a\nb\n"],
	'wrapped paragraph'       => ['<div><p>a</p></div><h1>b</h1>', "a\nb\n"],
	'empty element before'    => ['<p>a</p><span></span><h1>b</h1>', "a\nb\n"],
	'inline text before'      => ['<div>a<h1>b</h1></div>', "a\nb\n"],
	'unterminated block'      => ['<div>a</div><h1>b</h1>', "a\nb\n"],
	'first nested header'     => ['<div>a<div><h1>b</h1></div></div>', "a\nb\n"],
	'single header'           => ['<h1>a</h1>', "a\n"]
];
$converter=new HtmlConverter();
foreach ($cases as $label => [$html, $expectedText]) {
	// Verify the text, so an extra empty line before the header fails the check.
	$delta=$converter->convert($html);
	if ($delta->toPlainText()!==$expectedText) {
		throw new RuntimeException($label . ': ' . $delta->toJson());
	}
}

// Keep the header format on the header line only.
$ops=$converter->convert('<h1>a</h1><h2>b</h2>')->getOps();
if (count($ops)!==4 || $ops[1]->getAttribute('header')!==1 || $ops[3]->getAttribute('header')!==2) {
	throw new RuntimeException('Header lines were not separated: ' . json_encode($ops));
}
// A custom paragraph converter without a line ending still gets the header on its own line.
$customConverter=new class extends HtmlConverter {
	public function __construct() {
		parent::__construct();
		array_unshift($this->converters, new class implements NodeConverterInterface {
			public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): DeltaOp {
				return DeltaOp::text($node->textContent);
			}

			public function getSupportedTags(): array {
				return ['p'];
			}
		});
	}
};
if ($customConverter->convert('<p>a</p><h1>b</h1>')->toPlainText()!=="a\nb\n") {
	throw new RuntimeException('custom converter: header merged into the preceding text.');
}
echo "Header boundary regression checks passed.\n";