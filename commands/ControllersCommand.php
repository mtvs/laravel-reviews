<?php

namespace Reviews\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ControllersCommand extends Command
{
	protected $signature = 'reviews:controllers';

    protected $description = 'Scaffold the reviews controllers';

    public function handle()
    {
        $filesystem = new Filesystem();

        $files = $filesystem->allFiles(__DIR__.'/../stubs/controllers');

        foreach($files as $file)
        {
        	$filesystem->copy(
        		$file->getPathName(),
        		app_path('Http/Controllers/').
        		Str::replaceLast('.stub', '.php', $file->getFileName())
        	);
        }

        $this->info('Reviews controllers generated successfully.');
    }
}
