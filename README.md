A PDF manipulator based on pdftk.

## Features

*PHPExtractor* is a PDF manipulation tool for PHP.

 * Concatenate pages from several PDF files into a new PDF file
 * Split a PDF into multiple one page PDF files
 * Convert PDF to text
 * Search PDF
 * Rebuild PDF by keyword(s)
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

#### burst

Split a PDF file into one file per page.

```php
$file = new File('/file/location/file.pdf');

PDFExtractor::burst($file, '/location/where/to/burst/');

```

#### cat

Concatenate pages from several PDF files into a new PDF file.

```php
$file = new File('/file/location/file.pdf');

PDFExtractor::cat(/location/of/pdf/files/, '*.pdf', 'location/where/to/save/name_of_new_file.pdf');

```

#### pdfToText

Concatenate pages from several PDF files into a new PDF file.

```php
$file = new File('/file/location/file.pdf');

PDFExtractor::pdfToText($file);

```

#### searchPDF

Search PDF file by keywoard(s).

```php
$file = new File('/file/location/file.pdf');

PDFExtractor::searchPDF($file, 'wordToSearchBy');

```

#### rebuildPDFByKeyword

Rebuild PDF(s) by keyword(s)

```php
$file = new File('/file/location/file.pdf');

PDFExtractor::rebuildPDFByKeyword(File $file, '/location/where/to/burst/', /location/where/to/rebuild/, 'keyword_to_rebuild_by');

```

#### relocateFile

Change location of specific file

```php
$file = new File('/file/location/file.pdf');

PDFExtractor::relocateFile($file, '/new/file/path/');

```

#### clearFolder

Delete all content from file.

```php
$file = new File('/file/location/file.pdf');
$excludedFiles = ['excluded_file.pdf'];

PDFExtractor::clearFolder('/path/to/directory/', $excludedFiles);

```

#### createDirectory

Create new directory.

```php
PDFExtractor::createDirectory('/path/to/directory/name/');

```


