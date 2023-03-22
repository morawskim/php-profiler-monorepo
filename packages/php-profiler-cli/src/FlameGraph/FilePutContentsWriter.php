<?php

namespace Mmo\PhpProfilerCli\FlameGraph;

use Mmo\PhpProfilerCli\Converter\CorrelationIdGenerator\CorrelationIdGeneratorInterface;

class FilePutContentsWriter implements FlameGraphWriterInterface
{
    /**
     * @var CorrelationIdGeneratorInterface
     */
    private $correlationIdGenerator;

    public function __construct(CorrelationIdGeneratorInterface $correlationIdGenerator)
    {
        $this->correlationIdGenerator = $correlationIdGenerator;
    }

    public function write(string $svgContent, $context): void
    {
        file_put_contents(
            sprintf($context . DIRECTORY_SEPARATOR . '%s.svg', $this->correlationIdGenerator->getCorrelationId()),
            $svgContent
        );
    }
}
