<?php

namespace Mmo\PhpProfiler;

use Traversable;

class Profiler implements \IteratorAggregate
{
    /**
     * @var TreeItem
     */
    private $activeTreeItem;

    /**
     * @var TreeItem
     */
    private $treeRootItem;

    public function __construct(string $name, array $metadata = [])
    {
        $span= new Span($name, $metadata);
        $this->treeRootItem = $this->activeTreeItem = new TreeItem(null, $span);
    }

    public function start(string $name, array $metadata = []): void
    {
        $tmp = new Span($name, $metadata);

        $childTree = new TreeItem($this->activeTreeItem, $tmp);
        $this->activeTreeItem->addChild($childTree);
        $this->activeTreeItem = $childTree;
    }

    public function startNested(string $name, array $metadata = []): void
    {
        $tmp = new Span($name, $metadata);

        $tmpTree = new TreeItem($this->activeTreeItem, $tmp);
        $this->activeTreeItem->addChild($tmpTree);
        $this->activeTreeItem = $tmpTree;
    }

    public function stop(): void
    {
        $this->activeTreeItem->getValue()->stop();
        $parent = $this->activeTreeItem->getParent();

        if (null !== $parent) {
            $this->activeTreeItem = $parent;
        } else {
            $this->activeTreeItem = $this->treeRootItem;
        }
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator([$this->treeRootItem]);
    }
}
