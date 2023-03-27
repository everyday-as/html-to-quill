<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class ParagraphConverter implements NodeConverterInterface
{
    /**
     * @return array<DeltaOp>
     */
    public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): array
    {
        $ops = $htmlConverter->convertChildren($node);

        $modifier = DeltaOp::text("\n");

        if (isset($node->attributes['align'])) {
            $modifier->setAttribute('align', $node->attributes['align']->value);
        }
        
        if (isset($node->attributes['class'])) {
            $class = $node->attributes['class'];
            $map = ['q-align-center' => 'center', 'q-align-right' => 'right', 'q-align-left' => 'left'];
            if (isset($map[$class])) {
                $modifier->setAttribute('align', $map[$class]);
            }
        }
        if ($modifier->isBlockModifier()) {
            $ops[] = $modifier;
        }

        return $ops;
    }

    /**
     * @return array<string>
     */
    public function getSupportedTags(): array
    {
        return ['p'];
    }
}
