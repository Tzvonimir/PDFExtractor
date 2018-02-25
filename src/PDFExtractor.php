<?php

namespace ZTomesic\PDFExtractor;

use Illuminate\Http\File;

class PDFExtractor extends Builder
{

    /**
     * Split PDF into multiple one page PDF files.
     *
     * @param File|PDF|string $file
     *
     * @return $this
     */
    public static function burst($file)
    {
        self::getInstance();

        return self::$_instance->splitPDF($file);
    }

    /**
     * Concatenate multiple PDF files into one PDF.
     *
     * @param string $path
     * @param string|array $fileNames
     *
     * @return $this
     */
    public static function cat($path = '', $fileNames = '*.pdf')
    {
        self::getInstance();

        return self::$_instance->mergePDF($path, $fileNames);
    }

    /**
     * Convert PDF to string.
     *
     * @param File|PDF|string $location
     *
     * @return string|array $pdfText
     */
    public static function PDFToText($location)
    {
        self::getInstance();

        return self::$_instance->convertPDFToText($location);
    }

    /**
     * Search PDF for specific keyword(s).
     *
     * @param File|PDF $file
     * @param string $keyword
     *
     * @return boolean
     */
    public static function searchPDF($file, $keyword)
    {
        self::getInstance();

        return self::$_instance->searchPDFByKeywords($file, $keyword);
    }

    /**
     * Change location of File.
     *
     * @param File|PDF|string $file
     * @param string $path
     *
     * @return $this
     */
    public static function relocateFile($file, $path)
    {
        self::getInstance();
        self::$_instance->changeFileLocation($path, $file);

        return self::$_instance;
    }

    /**
     * Delete folder content.
     *
     * @param string $path
     * @param array $excludedFiles
     *
     * @return $this
     */
    public static function clearFolder($path, Array $excludedFiles = [])
    {
        self::getInstance();
        self::$_instance->clearFolderContent($path, $excludedFiles);

        return self::$_instance;
    }

    /**
     * Remove directory and content.
     *
     * @param string $path
     *
     * @return $this
     */
    public static function removeDirectory($path)
    {
        self::getInstance();
        self::$_instance->destroyDirectory($path);

        return self::$_instance;
    }

    /**
     * Create new directory.
     *
     * @param string $path
     *
     * @return $this
     */
    public static function createDirectory($path)
    {
        self::getInstance();
        self::$_instance->createNewDirectory($path);

        return self::$_instance;
    }

    /**
     * Create new directory.
     *
     * @param string|PDF|File $file
     *
     * @return $this
     */
    public static function extractMetadata($file)
    {
        self::getInstance();
        self::$_instance->extractMeta($file);

        return self::$_instance;
    }

    /**
     * Rebuild PDF by keyword(s).
     *
     * @param File|PDF $file
     * @param string $burstPath
     * @param string $rebuildPath
     * @param array $keywords
     *
     * @return $this
     */
    public static function rebuildPDFByKeyword($file, $burstPath, $rebuildPath, Array $keywords) {
        self::getInstance();
        self::$_instance->splitPDF($file)->save($burstPath);

        self::$_instance->searchMultiplePDFByKeywords($burstPath, $keywords)
                        ->changeFileLocation($rebuildPath)
                        ->mergePDFByKeywords();

        self::$_instance->createNewDirectory($rebuildPath . 'failed_documents/');

        self::$_instance->mergePDF($burstPath, '*.pdf')->save($rebuildPath . 'failed_documents/', 'failed.pdf');

        self::$_instance->clearFolderContent($burstPath);

        return self::$_instance;
    }

}
