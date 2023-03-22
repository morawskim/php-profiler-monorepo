<?php

namespace Mmo\PhpProfilerCli\Reader;

use Mmo\PhpProfiler\Serializer\SerializerInterface;

class FilePutContentsReader implements ReaderInterface
{
    /**
     * @var string
     */
    private $filePath;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(string $filePath, SerializerInterface $serializer)
    {
        $this->filePath = $filePath;
        $this->serializer = $serializer;
    }

    public function readProfilerData(): iterable
    {
        $handler = fopen($this->filePath, 'rb');
        while (!feof($handler)) {
            $line = trim(fgets($handler));

            if ('' === $line) {
                continue;
            }

            $probe = $this->serializer->deserialize($line);
            yield $probe;
        }
    }
}
