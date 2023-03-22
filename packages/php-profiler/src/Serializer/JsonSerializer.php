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
        $stack = new \SplStack();
        $stack->push([]);

        return $this->fromArray($decodedJson, $stack);
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

    private function fromArray(array $data, \SplStack $stack): Probe
    {
        $obj = new Probe(
            $data['span']['name'] ?? '',
            $data['span']['duration'] ?? 0,
            array_merge($stack->top(), $data['span']['metadata'] ?? [])
        );

        $stack->push($obj->getMetadata());
        foreach ($data['children'] ?? [] as $child) {
            $obj->addChild(static::fromArray($child, $stack));
        }
        $stack->pop();

        return $obj;
    }
}
