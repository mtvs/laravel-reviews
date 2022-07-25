<?php

namespace Mtvs\Reviews\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ReviewsUiCommand extends Command
{
	protected $signature = 'reviews:ui';

    protected $description = 'Install the reviews UI files';

    public function handle()
    {
        $filesystem = new Filesystem();

        $filesystem->copyDirectory(
            __DIR__.'/../stubs/ui', 
            resource_path('js/components/reviews')
        );


        if (! $filesystem->isDirectory(public_path('css'))) {
            $filesystem->makeDirectory(public_path('css'));
        }

        $filesystem->copy(
            __DIR__.'/../stubs/resources/css/reviews.css',
            public_path('css/reviews.css')
        );

        if (! $filesystem->isDirectory(public_path('font'))) {
            $filesystem->makeDirectory(public_path('font'));
        }        

        $filesystem->copyDirectory(
            __DIR__.'/../stubs/resources/font',
            public_path('font')
        );
        
        $this->info('Reviews UI files installed successfully.');
        
        $this->comment('Don\'t forget to register the UI components.');
        $this->comment('Don\'t forget to include the style sheet file.');
    }
}
