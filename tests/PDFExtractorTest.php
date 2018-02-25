<?php

use ZTomesic\PDFExtractor\PDFExtractor;
use ZTomesic\PDFExtractor\PDF;

class PDFExtractorTest extends \PHPUnit\Framework\TestCase
{

    public function testShouldCreateDirectory()
    {
        PDFExtractor::createDirectory(__DIR__ . '/test/');
        $this->assertFileExists(__DIR__ . '/test/');
    }

    public function testShouldBurstFile()
    {
        $file = new PDF($this->getTestDocument());
        PDFExtractor::burst($file)->save( __DIR__ . '/test/');
        $this->assertFileExists(__DIR__ . '/test/');
        $this->assertFileExists(__DIR__ . '/test/pdf_01.pdf');
        $this->assertFileExists(__DIR__ . '/test/pdf_02.pdf');
        $this->assertFileExists(__DIR__ . '/test/pdf_03.pdf');
        $this->assertFileExists(__DIR__ . '/test/pdf_04.pdf');
        $this->assertFileExists(__DIR__ . '/test/pdf_05.pdf');
    }

    public function testShouldCatFiles()
    {
        PDFExtractor::cat(__DIR__ . '/test/', '*.pdf')
            ->save(__DIR__ . '/test/', 'new.pdf');
        $this->assertFileExists(__DIR__ . '/test/new.pdf');
    }

    public function testShouldSearchPDF()
    {
        $file = new PDF(__DIR__ . '/test/pdf_01.pdf');
        $this->assertTrue(PDFExtractor::searchPDF($file, '000001'));
    }

    public function testShouldGetTextFromPDF()
    {
        $file = new PDF(__DIR__ . '/test/pdf_01.pdf');
        $this->assertNotEmpty(PDFExtractor::pdfToText($file));
    }

    public function testShouldDeleteAllFromDirectory()
    {
        PDFExtractor::clearFolder(__DIR__ . '/test');
        $this->assertFileNotExists(__DIR__ . '/test/new.pdf');
    }

    public function testShouldRebuildPDF()
    {
        $file = new PDF($this->getTestDocument());
        PDFExtractor::rebuildPDFByKeyword($file, __DIR__ . '/test/', __DIR__ . '/test/', ['000001']);
        $this->assertFileExists(__DIR__ . '/test/000001/000001.pdf');
    }

    public function testShouldExtractMetadata()
    {
        PDFExtractor::extractMetadata($this->getTestDocument())
            ->save(__DIR__ . '/test/', 'metadata.txt');
        $this->assertFileExists(__DIR__ . '/test/metadata.txt');
    }

    public function testShouldConvertMetadataToString()
    {
        $metadata = PDFExtractor::extractMetadata($this->getTestDocument())
            ->save(__DIR__ . '/test/', 'metadata.txt')->toString();
        $this->assertTrue(is_string($metadata));
    }

    public function testShouldConvertMetadataToArray()
    {
        $metadata = PDFExtractor::extractMetadata($this->getTestDocument())
            ->save(__DIR__ . '/test/', 'metadata.txt')->toArray();
        $this->assertTrue(is_array($metadata));
    }

    public function testShouldRemoveDirectory()
    {
        PDFExtractor::removeDirectory(__DIR__ . '/test/');
        $this->assertFileNotExists(__DIR__ . '/test/');
    }

    protected function getTestDocument()
    {
        return __DIR__ . '/files/test.pdf';
    }
}
