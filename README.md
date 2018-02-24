[![Build Status](https://travis-ci.org/Tzvonimir/PDFExtractor.svg?branch=master)](https://travis-ci.org/Tzvonimir/PDFExtractor)

A PDF manipulator based on pdftk.

## Features

*PHPExtractor* is a PDF manipulation tool for PHP.

 * Concatenate pages from several PDF files into a new PDF file
 * Split a PDF into multiple one page PDF files
 * Convert PDF to text
 * Search PDF
 * Rebuild PDF by keyword(s)
 * Extract PDF by keyword(s)
 * Create new directory
 * Delete all files from directory

## Requirements

 * The `pdftk` command must be installed on your system

## Installation

You should use use [composer](https://getcomposer.org/) to install this library.

```
composer require ztomesic/pdfextractor
```

## Examples

use ZTomesic\PDFExtractor\PDFExtractor;

### PDF File class

use ZTomesic\PDFExtractor\PDF;

$pdf = new PDF('/file/location/file.pdf');

#### Burst

Split a PDF file into one file per page.

```php
$file = new PDF('/file/location/file.pdf');

PDFExtractor::burst($file)->save('/location/where/to/burst/');

```

#### Cat

Concatenate pages from several PDF files into a new PDF file.

```php
PDFExtractor::cat(/location/of/pdf/files/, '*.pdf')->save('location/where/to/save/', 'name_of_new_file.pdf');

```

#### PDF To Text

Convert PDF to text.

```php
$pdf = new PDF('/file/location/file.pdf');

PDFExtractor::PDFToText($pdf);

```

#### Search PDF

Search PDF file by keywoard(s).

```php
$pdf = new PDF('/file/location/file.pdf');

PDFExtractor::searchPDF($pdf, 'wordToSearchBy');

```

#### Rebuild PDF By Keyword

Rebuild PDF(s) by keyword(s)

```php
$pdf = new PDF('/file/location/file.pdf');

PDFExtractor::rebuildPDFByKeyword($pdf, '/location/where/to/burst/', '/location/where/to/rebuild/', 'keyword_to_rebuild_by');

```

#### Relocate File

Change location of specific file

```php
$pdf = new PDF('/file/location/file.pdf');

PDFExtractor::relocateFile($pdf, '/new/file/path/');

```

#### Clear Folder

Delete all content from folder.

```php
$excludedFiles = ['excluded_file.pdf'];

PDFExtractor::clearFolder('/path/to/directory/', $excludedFiles);

```

#### Remove Directory

Delete directory and content.

```php
PDFExtractor::removeDirectory('/path/to/directory/');

```

#### Create Directory

Create new directory.

```php
PDFExtractor::createDirectory('/path/to/directory/name/');

```

#### Search and rebuild

Search PDF(s) and rebuild by keyword.

```php
$pdf = new PDF('/file/location/file.pdf');
$PDFExtractor = PDFExtractor::burst($file)->save('/location/where/to/burst/');

$PDFExtractor->searchMultiplePDFByKeywords('/location/where/to/burst/', 'wordToSearchBy')
             ->changeFileLocation('/location/where/to/rebuild/')
             ->mergePDFByKeywords();

```


