<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop_product;
class Delete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

	app('App\Http\Controllers\TwoSyncDataController')->sync_image();

		}
}
