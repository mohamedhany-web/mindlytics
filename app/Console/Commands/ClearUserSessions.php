<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class ClearUserSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:clear {user_id? : ID of specific user to clear, or leave empty for all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear user session cache to fix login issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        if ($userId) {
            // مسح جلسة مستخدم محدد
            $cacheKey = "user_session_{$userId}";
            Cache::forget($cacheKey);
            $this->info("تم مسح جلسة المستخدم رقم: {$userId}");
        } else {
            // مسح جميع الجلسات
            $users = User::all();
            $count = 0;
            
            foreach ($users as $user) {
                $cacheKey = "user_session_{$user->id}";
                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                    $count++;
                }
            }
            
            $this->info("تم مسح {$count} جلسة مستخدم من الكاش");
        }

        return 0;
    }
}
