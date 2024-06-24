<?php

namespace App\Console\Commands;

use App\Jobs\BatchJob;
use App\Jobs\ChainedJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ChainTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:chain-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batch = [];
        for ($i=0; $i < 5; $i++) {
            $batch[] = new BatchJob($i +1);
        }

        // throws exception, closure not called
        Bus::chain([
            new ChainedJob,
            Bus::batch($batch),
            function () {
                logger('after batch');
            }
        ])->dispatch();

        // works fine
        Bus::chain([
            new ChainedJob,
            Bus::batch($batch),
            new ChainedJob,
            function () {
                logger('after batch');
            }
        ])->dispatch();
    }
}
