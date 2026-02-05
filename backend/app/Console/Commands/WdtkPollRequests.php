<?php

namespace App\Console\Commands;

use App\Services\WdtkService;
use Illuminate\Console\Command;

class WdtkPollRequests extends Command
{
    protected $signature = 'wdtk:poll';

    protected $description = 'Poll WhatDoTheyKnow for status updates on tracked FOI requests';

    public function handle(WdtkService $wdtk): int
    {
        $this->info('Polling WhatDoTheyKnow for status updates...');

        $updates = $wdtk->pollAllTracked();

        if (empty($updates)) {
            $this->info('No status changes detected.');
            return self::SUCCESS;
        }

        $this->info(count($updates) . ' status change(s) detected:');

        foreach ($updates as $update) {
            $request = $update->foiRequest;
            $this->line(
                "  [{$request->title}] {$update->old_status} -> {$update->new_status}"
            );
        }

        return self::SUCCESS;
    }
}
