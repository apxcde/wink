<?php

namespace Wink\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'wink:install';

    protected $description = 'Install all of the Wink resources';

    public function handle()
    {
        $this->comment('Publishing Wink Assets...');
        $this->callSilent('vendor:publish', ['--tag' => 'wink-assets']);

        $this->comment('Publishing Wink Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'wink-config']);

        $this->info('Wink was installed successfully.');
    }
}
