<?php

namespace Mmo\PhpProfiler\Serializer;

use Mmo\PhpProfiler\Profiler;
use Mmo\PhpProfiler\TreeItem;

class JsonSerializer implements SerializerInterface
{
    public function serialize(Profiler $profiler): string
    {
        $data = [];
        foreach ($profiler as $treeItem) {
            $data = $this->serializeTree($treeItem);
        }

        return json_encode($data);
    }

    private function serializeTree(TreeItem $treeItem): array
    {
        $children = [];

        if (count($treeItem->getChildren())) {
            foreach ($treeItem->getChildren() as $child) {
                $children[] = $this->serializeTree($child);
            }
        }

        return [
            'span' => [
                'name' => $treeItem->getValue()->getName(),
                'duration' => $treeItem->getValue()->getDuration(),
                'metadata' => $treeItem->getValue()->getMetadata(),
            ],
            'children' => $children
        ];
    }
}
