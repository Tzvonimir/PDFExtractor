<?php

use ZTomesic\PDFExtractor\PDFExtractor;

class PDFExtractorTest extends \PHPUnit\Framework\TestCase
{

    public function testShouldCreateDirectory() {
        PDFExtractor::createDirectory(__DIR__ . '/test/');
        $this->assertDirectoryExists(__DIR__ . '/test/');
    }

    protected function getTestDocument()
    {
        return __DIR__ . '/files/test.pdf';
    }
}
