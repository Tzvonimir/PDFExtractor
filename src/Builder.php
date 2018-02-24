<?php

namespace ZTomesic\PDFExtractor;

use Illuminate\Http\File;

class Builder
{
    protected $command;
    protected $suffix = 'pdf_%02d.pdf';
    protected $PDFList = [];

    protected static $_instance = null;

    public static function getInstance ()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function splitPDF($file)
    {
        if(!($file instanceof File) && !($file instanceof PDF)) {
            $file = new PDF($file);
        }

        $this->command = 'pdftk ' . $file->getPath() . '/' . $file->getFilename() . ' burst';

        return $this;
    }

    public function save($outputLocation = null, $outputFilename = null)
    {
        if(strpos($this->command, 'burst') !== false) {
            if (is_null($outputLocation) && !is_null($outputFilename)) {
                $this->command .= ' output ' . $this->reconstructFilename($outputFilename);
            } elseif (!is_null($outputLocation) && is_null($outputFilename)) {
                $this->command .= ' output ' . $outputLocation . $this->suffix;
            } elseif (!is_null($outputLocation) && !is_null($outputFilename)) {
                $this->command .= ' output ' . $outputLocation . $this->reconstructFilename($outputFilename);
            }
        } else {
            $this->command .= ' output ' . $outputLocation . $outputFilename;
        }

        return $this->execute($this->command);
    }

    public function reconstructFilename($filename) {
        $parts = preg_split('@(?=.pdf)@', $filename);

        if (isset($parts[0]) && isset($parts[1]) && $parts[1] === '.pdf') {
            if ($parts[0] !== '') {
                return  $parts[0] . '_%02d' . $parts[1];
            }
        }

        return $this->suffix;
    }

    public function mergePDF($path = '', $fileNames = '*.pdf')
    {
        if(is_array($fileNames)) {
            foreach ($fileNames as $fileName) {
                $path = $path . ' ' . $fileName;
            }
        } else {
            $path = $path . $fileNames;
        }

        $this->command = 'pdftk ' .  $path .  ' cat';

        return $this;
    }

    public function convertPDFToText($location)
    {
        $pdfToText = new PDFToText();

        if($location instanceof File || $location instanceof PDF) {
            return $pdfToText->pdf2text($location->getPath() . '/' . $location->getFilename());
        } else {
            $pdfText = [];

            $fileList = glob($location . '*');
            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            foreach($fileList as $filename){

                if(finfo_file($finfo, $filename) === 'application/pdf') {
                    $pdfText[] = $pdfToText->pdf2text($filename);
                }
            }
            finfo_close($finfo);
            return $pdfText;
        }
    }

    public function searchPDFByKeywords($file, $keyword)
    {
        if($file instanceof File || $file instanceof PDF) {
            $pdfContent = $this->convertPDFToText($file);
            if (strpos($pdfContent, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function createNewDirectory($path)
    {
        $path = ($path instanceof PDF || $path instanceof File) ? $path->getPath() . '/' . $path->getFilename() : $path;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    public function changeFileLocation($path, $file = null)
    {
        $newFileLocation = [];
        $file = ($file) ? $file : $this->PDFList;
        self::$_instance->createNewDirectory($path);
        if($file instanceof File || $file instanceof PDF) {
            rename($file->getPath() . '/' . $file->getFilename(), $path . $file->getFilename());
        } else {
            if (is_array($file)) {
                foreach ($file as $fileLocation => $name) {
                    $pdf = new PDF($fileLocation);
                    self::$_instance->createNewDirectory($path . $name . '/');
                    $newFileLocation[$path] = $name;
                    $this->changeFileLocation($path . $name . '/', $pdf);
                }
                $this->PDFList = $newFileLocation;
            } else {
                rename($file, $path . substr($file, strrpos($file, '/') + 1));
            }
        }

        return $this;
    }

    public function clearFolderContent($path, Array $excludedFiles = [])
    {
        foreach( glob($path . '/*') as $file ) {
            if (!in_array(basename($file), $excludedFiles) && is_file($file)) {
                unlink($file);
            }
        }
    }

    public function destroyDirectory($path)
    {
        if(is_dir($path)){
            $files = glob( $path . '*', GLOB_MARK );

            foreach( $files as $file ) {
                $this->destroyDirectory($file);
            }
            rmdir( $path );
        } elseif(is_file($path)) {
            unlink( $path );
        }
    }

    public function mergePDFByKeywords($PDFList = null) {
        $PDFList = ($PDFList) ? $PDFList : $this->PDFList;
        foreach ($PDFList as $fileLocation => $name) {
            $directory = $fileLocation . $name . '/';
            //dd($directory);
            if (file_exists($directory)) {
                self::$_instance->mergePDF($directory, '*.pdf')->save($directory, $name . '.pdf');
            }
            self::$_instance->clearFolderContent($directory, [$name . '.pdf']);
        }
    }

    public function searchMultiplePDFByKeywords($path, array $keywords)
    {
        $fileList = glob($path . '*');
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);

        foreach($fileList as $filename){
            if(finfo_file($fileInfo, $filename) === 'application/pdf') {
                $file = new PDF($filename);
                foreach ($keywords as $keyword) {
                    if(self::$_instance->searchPDFByKeywords($file, $keyword)) {
                        $this->PDFList[$file->getFullPath()] = $keyword;
                    }
                }
            }
        }

        return $this;
    }

    public function execute() {
        shell_exec($this->command);

        return $this;
    }
}
