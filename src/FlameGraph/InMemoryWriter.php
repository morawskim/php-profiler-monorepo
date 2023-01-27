<?php

namespace Mmo\PhpProfiler\FlameGraph;

class InMemoryWriter implements FlameGraphWriterInterface
{
    /**
     * @var string[]
     */
    private $data;

    public function write(string $svgContent, $context): void
    {
        $this->data[] = $svgContent;
    }

    /**
     * @return string[]
     */
    public function getData(): array
    {
        return $this->data;
    }
}
