<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DailyEmailSenderService;
use Illuminate\Support\Carbon;

class DailyEmailSenderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-daily-meetings-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily meetings email to all users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new DailyEmailSenderService)->sendUsersDailyEmail(new Carbon('2022-07-01 09:30:00.000'));

        return Command::SUCCESS;
    }
}
