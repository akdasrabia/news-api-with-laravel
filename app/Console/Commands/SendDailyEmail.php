<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\News;
use App\Notifications\InvoicePaid;
use App\Notifications\NewsNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendDailyEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-email';

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
        $today = Carbon::today();
        $users = User::get();
        $news = News::where('is_deleted', false)->whereDate('created_at', $today)->get(); 

        foreach($users as $user) {
            Notification::send($user, new NewsNotification($news));
        }
    }
}
