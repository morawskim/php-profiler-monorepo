<?php

namespace Mmo\PhpProfiler;

class TreeItem
{
    /**
     * @var TreeItem|null
     */
    private $parent;

    /**
     * @var Span
     */
    private $value;

    /**
     * @var TreeItem[]
     */
    private $children = [];

    public function __construct(?TreeItem $parent, Span $value)
    {
        $this->parent = $parent;
        $this->value = $value;
    }

    /**
     * @return Span|null
     */
    public function getParent(): ?TreeItem
    {
        return $this->parent;
    }

    /**
     * @return Span
     */
    public function getValue(): Span
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(TreeItem $span): void
    {
        $this->children[] = $span;
    }
}
