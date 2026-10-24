<?php

use Everyday\HtmlToQuill\HtmlConverter;

// Load this checkout with Composer dependencies, optionally supplied by the caller.
$loader=require ($argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php');
$loader->addPsr4('Everyday\\HtmlToQuill\\', dirname(__DIR__) . '/src/', true);

// Inline code must not become a block solely because nested formatting splits operations.
$cases=[
	'plain code'       => ['<code>ab</code>', "ab\n", 'code'],
	'formatted prefix' => ['<code><b>a</b>b</code>', "ab\n", 'code'],
	'formatted suffix' => ['<code>a<i>b</i></code>', "ab\n", 'code'],
	'multiple formats' => ['<code><b>a</b><i>b</i></code>', "ab\n", 'code'],
	'multiline code'   => ['<code>a<br />b</code>', "a\nb\n", 'code-block']
];
$converter=new HtmlConverter();
foreach ($cases as $label => [$html, $expectedText, $attribute]) {
	// Verify the rendered content and code format on every non-newline text operation.
	$delta=$converter->convert($html);
	if ($delta->toPlainText()!==$expectedText) {
		throw new RuntimeException($label . ': text changed.');
	}
	foreach ($delta->getOps() as $op) {
		if ($op->getInsert()!=="\n" && $op->getAttribute($attribute)!==true) {
			throw new RuntimeException($label . ': incorrect code format.');
		}
		if ($attribute==='code' && $op->hasAttribute('code-block')) {
			throw new RuntimeException($label . ': inline code became a block.');
		}
	}
}

// Retain the original inline formatting inside the code span.
$ops=$converter->convert('<code><b>a</b>b</code>')->getOps();
if ($ops[0]->getAttribute('bold')!==true || $ops[0]->getAttribute('code')!==true) {
	throw new RuntimeException('Nested bold formatting was lost.');
}
echo "Inline code regression checks passed.\n";