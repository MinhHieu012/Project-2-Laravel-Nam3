<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ResetCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        DB::table('appointment_schedules')
            ->whereMonth('dates', Carbon::now()->subMonth())
            ->update(['cancelled' => 0]);

        DB::table('appointment_schedules')
            ->whereMonth('dates', Carbon::now()->subMonth())
            ->update(['payment_status_id' => null]);

        DB::table('accounts')
            ->whereMonth('created_at', Carbon::now()->subMonth())
            ->update(['levels' => null]);
    }
}
