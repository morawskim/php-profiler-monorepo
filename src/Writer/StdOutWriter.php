<?php

namespace Mmo\PhpProfiler\Writer;

use Mmo\PhpProfiler\Profiler;
use Mmo\PhpProfiler\TreeItem;

class StdOutWriter implements WriterInterface
{
    public function writeProfileData(Profiler $profiler): void
    {
        foreach ($profiler as $a) {
            $this->dumpTree($a, 0);
        }
    }

    private function dumpTree(TreeItem $treeItem, int $depth): void
    {
        $metadata = [];
        foreach ($treeItem->getValue()->getMetadata() as $key => $value) {
            $metadata[] = sprintf('%s=%s', $key, $value);
        }

        printf(
            "%s%s [%s] %dms\n",
            str_repeat(' ', 2 * $depth),
            $treeItem->getValue()->getName(),
            implode(' ', $metadata),
            (int)$treeItem->getValue()->getDuration()
        );
        if (count($treeItem->getChildren())) {
            foreach ($treeItem->getChildren() as $child) {
                $this->dumpTree($child, $depth + 1);
            }
        }
    }
}
