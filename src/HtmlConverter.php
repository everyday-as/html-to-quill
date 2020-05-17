<?php

namespace Everyday\HtmlToQuill;

use Everyday\HtmlToQuill\Converters\NodeConverterInterface;
use Everyday\QuillDelta\Delta;
use Everyday\QuillDelta\DeltaOp;
use Symfony\Component\DomCrawler\Crawler;

class HtmlConverter implements HtmlConverterInterface
{
    /**
     * @var NodeConverterInterface[]
     */
    private $converters;
    private $tag_converter_cache = [];

    public function __construct()
    {
        $this->converters = [
            new Converters\BreakConverter(),
            new Converters\CodeConverter(),
            new Converters\EmphasisConverter(),
            new Converters\HeaderConverter(),
            new Converters\ImageConverter(),
            new Converters\LinkConverter(),
            new Converters\ParagraphConverter(),
            new Converters\TextConverter(),
        ];
    }

    /**
     * Convert
     *
     * @see HtmlConverter::convert
     *
     * @param string $html
     *
     * @return string The Markdown version of the html
     */
    public function __invoke($html)
    {
        return $this->convert($html);
    }

    /**
     * {@inheritdoc}
     */
    public function convert($html): Delta
    {
        if (empty(trim($html))) {
            return new Delta([]);
        }

        /** @var DeltaOp[] $ops */
        $ops = [];
        $crawler = new Crawler($html);

        foreach ($crawler->children() as $child) {
            $child->normalize();

            $ops = array_merge($ops, $this->convertChildren($child));
        }

        if (empty($ops)) {
            $ops[] = DeltaOp::text("\n");
        }

        if (!$ops[0]->isEmbed() && !$ops[0]->isBlockModifier()) {
            $ops[0]->setInsert(ltrim($ops[0]->getInsert()));
        }

        $delta = new Delta($ops);

        $delta->compact();

        return $delta;
    }

    /**
     * Convert to Quill
     *
     * @param \DOMNode $node
     *
     * @return DeltaOp[] The converted HTML as Quill DeltaOps
     */
    public function convertChildren(\DOMNode $node): array
    {
        $ops = [];

        if (!$node->hasChildNodes()) {
            return $ops;
        }

        foreach ($node->childNodes as $childNode) {
            $converter = $this->getConverter($childNode->nodeName);

            if (null === $converter) {
                $ops = array_merge($ops, $this->convertChildren($childNode));

                continue;
            }

            $childNodeOps = $converter->convert($childNode, $this) ?? [];

            $ops = array_merge($ops, is_array($childNodeOps) ? $childNodeOps : [$childNodeOps]);
        }

        return $ops;
    }

    /**
     * @param $nodeName
     *
     * @return NodeConverterInterface|mixed|null
     */
    protected function getConverter(string $nodeName)
    {
        if (isset($this->tag_converter_cache[$nodeName])) {
            return $this->tag_converter_cache[$nodeName];
        }

        foreach ($this->converters as $converter) {
            if (in_array($nodeName, $converter->getSupportedTags())) {
                return $this->tag_converter_cache[$nodeName] =& $converter;
            }
        }

        return null;
    }
}
