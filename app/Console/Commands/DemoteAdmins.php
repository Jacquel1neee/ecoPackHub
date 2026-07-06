<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class DemoteAdmins extends Command
{
    protected $signature = 'hierarchy:demote-admins';

    protected $description = 'Check admin group sales and demote if they fall below level requirements every 90 days.';

    public function handle(): int
    {
        $this->info('Running admin demotion checks...');

        $admins = User::where('role', 1)->get();

        foreach ($admins as $admin) {
            $last = $admin->last_sales_check ? Carbon::parse($admin->last_sales_check) : null;

            // If never checked or checked more than 90 days ago
            if (!$last || $last->diffInDays(now()) >= 90) {
                $groupSales = $admin->group_sales;

                // Demote until group sales meets requirement or reach level 1
                while ($admin->level > 1) {
                    $minForLevel = User::getLevelSalesRequirement($admin->level);
                    if ($groupSales >= $minForLevel) {
                        break;
                    }

                    $admin->level = max(1, $admin->level - 1);
                }

                $admin->last_sales_check = now();
                $admin->save();
            }
        }

        $this->info('Demotion checks completed.');

        return Command::SUCCESS;
    }
}
