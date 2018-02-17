<?php

namespace ZTomesic\PDFExtractor;

use Illuminate\Support\ServiceProvider;

class PDFExtractorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('ZTomesic\PDFExtractor\PDFExtractor');
    }
}
