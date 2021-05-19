<?php

namespace Neputer\Supports\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class ClearCommand
 * @package Neputer\Supports\Console\Commands
 */
final class ClearCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neputer:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear cache, views and logs';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('view:clear');
        $this->call('optimize');

        if (function_exists('exec')) :
            exec('truncate -s 0 storage/logs/*.log', $output, $result);
            if (!$result) {
                $outputs[] = 'Log cleared successfully!';
            } else {
                $outputs[] = 'Log clear failed!';
            }
        else :
            $outputs[] = 'Log clear command cannot be init! exec is disabled!';
        endif;
        $this->info(implode('', $outputs));
        $this->info('All done successfully ! Love :) ');
    }

}
