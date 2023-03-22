<?php

namespace Mmo\PhpProfiler\Writer;

use Mmo\PhpProfiler\Profiler;
use Mmo\PhpProfiler\Serializer\SerializerInterface;

class FilePutContentsWriter implements WriterInterface
{
    private $serializer;
    private $fileName;

    public function __construct(SerializerInterface $serializer, string $fileName)
    {
        $this->serializer = $serializer;
        $this->fileName = $fileName;
    }

    public function writeProfileData(Profiler $profiler): void
    {
        $json = $this->serializer->serialize($profiler) . "\n";
        file_put_contents($this->fileName, $json, FILE_APPEND);
    }
}
