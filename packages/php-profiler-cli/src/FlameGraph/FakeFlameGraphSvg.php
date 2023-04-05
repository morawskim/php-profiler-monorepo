<?php

namespace Mmo\PhpProfilerCli\FlameGraph;

class FakeFlameGraphSvg implements FlameGraphSvgInterface
{
    public function createSVG(string $content): string
    {
        return '<fake-svg-image>' . $content . '</fake-svg-image>';
    }
}
