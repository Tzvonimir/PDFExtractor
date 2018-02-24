<?php

namespace ZTomesic\PDFExtractor;

class PDF
{

    private $path;

    public function __construct($path)
    {
        if ($path && !is_file($path)) {
            return false;
        }

        $this->path = realpath($path);
    }

    public function getFullPath()
    {
        return $this->path;
    }

    public function getFilename()
    {
        return basename($this->path);
    }

    public function getPath()
    {
        return dirname($this->path);
    }
}
