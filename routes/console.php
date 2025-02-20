<?php

use App\Actions\CheckForScheduledSpeedtests;
use Illuminate\Support\Facades\Schedule;

/**
 * Checks if Result model records should be pruned.
 */
if (config('speedtest.prune_results_older_than') > 0) {
    Schedule::command('model:prune', [
        '--model' => [\App\Models\Result::class],
    ])->daily();
}

/**
 * Checked for new versions weekly on Thursday because
 * I usually do releases on Thursday or Friday.
 */
Schedule::command('app:version')
    ->weeklyOn(5);

/**
 * Nightly maintenance
 */
Schedule::daily()
    ->group(function () {
        Schedule::command('queue:prune-batches --hours=48');
        Schedule::command('queue:prune-failed --hours=48');
    });

/**
 * Check for scheduled speedtests.
 */
Schedule::everyMinute()
    ->group(function () {
        Schedule::call(fn () => CheckForScheduledSpeedtests::run());
    });
