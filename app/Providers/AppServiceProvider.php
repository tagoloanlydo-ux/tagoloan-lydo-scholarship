<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\ApplicationPersonnel; // added import

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share a default $badgeCount and $pendingScreening with all views to avoid undefined variable errors.
        View::composer('*', function ($view) {
            $badgeCount = 0;
            $pendingScreening = 0;
            $pendingRenewals = 0;

            // If a lydo staff user is in session, attempt to compute unread notification count
            $lydopers = session('lydopers');
            if ($lydopers && isset($lydopers->lydopers_id)) {
                try {
                    $badgeCount = DB::table('tbl_notifications')
                        ->where('notif_to_id', $lydopers->lydopers_id)
                        ->where('is_read', 0)
                        ->count();
                } catch (\Throwable $e) {
                    $badgeCount = 0;
                }
            }

            // Compute pending screening count (safe fallback on DB errors)
            try {
                $pendingScreening = ApplicationPersonnel::where('status', 'Waiting')->count();
            } catch (\Throwable $e) {
                $pendingScreening = 0;
            }

            // Compute pending renewals count (safe, tolerant to different table/column names)
            try {
                $schema = DB::getSchemaBuilder();
                if ($schema->hasTable('renewals')) {
                    $pendingRenewals = DB::table('renewals')
                        ->where(function ($q) {
                            $q->where('status', 'Pending')->orWhere('renewal_status', 'Pending')->orWhere('status', 'pending');
                        })->count();
                } elseif ($schema->hasTable('tbl_renewals')) {
                    $pendingRenewals = DB::table('tbl_renewals')
                        ->where(function ($q) {
                            $q->where('status', 'Pending')->orWhere('renewal_status', 'Pending')->orWhere('status', 'pending');
                        })->count();
                } elseif (class_exists(\App\Models\Renewal::class)) {
                    $pendingRenewals = \App\Models\Renewal::where(function ($q) {
                        $q->where('status', 'Pending')->orWhere('renewal_status', 'Pending')->orWhere('status', 'pending');
                    })->count();
                } else {
                    $pendingRenewals = 0;
                }
            } catch (\Throwable $e) {
                $pendingRenewals = 0;
            }

            $view->with('badgeCount', $badgeCount)
                 ->with('pendingScreening', $pendingScreening)
                 ->with('pendingRenewals', $pendingRenewals);
        });
    }
}
