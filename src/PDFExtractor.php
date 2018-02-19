<?php

namespace ZTomesic\PDFExtractor;

use Illuminate\Http\File;

class PDFExtractor
{
    /**
     * Split PDF into multiple one page PDF files.
     *
     * @param File|PDF|string $file
     * @param string $outputLocation
     *
     */
    public static function burst($file, $outputLocation = null)
    {
        if($file instanceof File || $file instanceof PDF) {
            if ($outputLocation) {
                $outputLocation = $outputLocation . 'pdf_%02d.pdf';
                shell_exec('pdftk ' . $file->getPath() . '/' . $file->getFilename() . ' burst output ' . $outputLocation);
            } else {
                shell_exec('pdftk ' . $file->getPath() . '/' . $file->getFilename() . ' burst');
            }
        } else {
            if ($outputLocation) {
                $outputLocation = $outputLocation . 'pdf_%02d.pdf';
                shell_exec('pdftk ' . $file . ' burst output ' . $outputLocation);
            } else {
                shell_exec('pdftk ' . $file . ' burst');
            }
        }
    }

    /**
     * Concatenate multiple PDF files into one PDF.
     *
     * @param string $path
     * @param string|array $fileNames
     * @param string $newFileName
     *
     */
    public static function cat($path = '', $fileNames = '*.pdf', $newFileName = 'new.pdf')
    {
        if(is_array($fileNames)) {
            foreach ($fileNames as $fileName) {
                $path = $path . ' ' . $fileName;
            }
        } else {
            $path = $path . $fileNames;
        }

        shell_exec('pdftk ' .  $path .  ' cat output ' . $newFileName);
    }

    /**
     * Convert PDF to string.
     *
     * @param File|PDF|string $location
     *
     * @return string|array $pdfText
     */
    public static function pdfToText($location)
    {
        $pdfToText = new PDFToText();
        if($location instanceof File || $location instanceof PDF) {
            return $pdfToText->pdf2text($location->getPath() . '/' . $location->getFilename());
        } else {
            $pdfText = [];

            //Get a list of file paths using the glob function.
            $fileList = glob($location . '*');

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            //Loop through the array that glob returned.
            foreach($fileList as $filename){

                if(finfo_file($finfo, $filename) === 'application/pdf') {
                    //dd($location . '/' . $filename);
                    $pdfText[] = $pdfToText->pdf2text($filename);
                }
            }
            finfo_close($finfo);
            return $pdfText;
        }
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
        if($file instanceof File || $file instanceof PDF) {
            $pdfContent = self::pdfToText($file);
            if (strpos($pdfContent, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Change location of File.
     *
     * @param File|PDF|string $file
     * @param string $path
     */
    public static function relocateFile($file, $path)
    {
        self::createDirectory($path);
        if($file instanceof File || $file instanceof PDF) {
            rename($file->getPath() . '/' . $file->getFilename(), $path . $file->getFilename());
        } else {
            rename($file, $path . substr($file, strrpos($file, '/') + 1));
        }
    }

    /**
     * Delete folder content.
     *
     * @param string $path
     * @param array $excludedFiles
     */
    public static function clearFolder($path, Array $excludedFiles = [])
    {
        foreach( glob($path . '/*') as $file ) {
            if( !in_array(basename($file), $excludedFiles) )
                unlink($file);
        }
    }

    /**
     * Remove directory and content.
     *
     * @param string $path
     */
    public static function removeDirectory($path) {
        if(is_dir($path)){
            $files = glob( $path . '*', GLOB_MARK );

            foreach( $files as $file ) {
                self::removeDirectory($file);
            }
            rmdir( $path );
        } elseif(is_file($path)) {
            unlink( $path );
        }
    }


    /**
     * Create new directory.
     *
     * @param string $path
     */
    public static function createDirectory($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    /**
     * Rebuild PDF by keyword(s).
     *
     * @param File|PDF $file
     * @param string $burstPath
     * @param string $rebuildPath
     * @param array $keyword
     */
    public static function rebuildPDFByKeyword($file, $burstPath, $rebuildPath, Array $keyword) {
        self::burst($file, $burstPath);
        self::createDirectory($rebuildPath);

        //Get a list of file paths using the glob function.
        $fileList = glob($burstPath . '*');

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        //Loop through the array that glob returned.
        foreach($fileList as $filename){
            if(finfo_file($finfo, $filename) === 'application/pdf') {
                $file = new PDF($filename);
                if(is_array($keyword)) {
                    foreach ($keyword as $key) {
                        $rebuildLocation = $rebuildPath . $key . '/';
                        if(self::searchPDF($file, $key)) {
                            self::createDirectory($rebuildLocation);
                            self::relocateFile($file, $rebuildLocation);
                            break;
                        }
                    }
                } else {
                    if(self::searchPDF($file, $keyword)) {
                        self::relocateFile($file, $rebuildPath);
                    }
                }
            }
        }

        foreach ($keyword as $key) {
            $directory = $rebuildPath . $key . '/';
            if (file_exists($directory)) {
                self::cat($directory, '*.pdf', $directory . $key . '.pdf');
            }
            self::clearFolder($directory, [$key . '.pdf']);
        }

        finfo_close($finfo);
    }
}
