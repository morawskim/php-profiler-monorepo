<?php

namespace Mmo\PhpProfiler\Serializer;

use Mmo\PhpProfiler\Dto\Probe;
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

    public function deserialize(string $serializedString): Probe
    {
        $decodedJson = json_decode($serializedString, true);

        return $this->fromArray($decodedJson);
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

    private function fromArray(array $data): Probe
    {
        $obj = new Probe(
            $data['span']['name'] ?? '',
            $data['span']['duration'] ?? 0,
            $data['span']['metadata'] ?? []
        );

        foreach ($data['children'] ?? [] as $child) {
            $obj->addChild(static::fromArray($child));
        }

        return $obj;
    }
}
