<?php

namespace App\Console\Commands;

use App\Services\WdtkService;
use Illuminate\Console\Command;

class WdtkImportRequest extends Command
{
    protected $signature = 'wdtk:import {url_title : The URL slug of the request (e.g. "my-foi-request-123")}';

    protected $description = 'Import and start tracking a WhatDoTheyKnow FOI request';

    public function handle(WdtkService $wdtk): int
    {
        $urlTitle = $this->argument('url_title');
        $this->info("Importing request: {$urlTitle}...");

        $request = $wdtk->importRequest($urlTitle);

        if (!$request) {
            $this->error("Failed to import request '{$urlTitle}'. Check the slug is correct.");
            return self::FAILURE;
        }

        $this->info("Imported successfully:");
        $this->line("  Title:     {$request->title}");
        $this->line("  Authority: {$request->authority_name}");
        $this->line("  Status:    {$request->status}");
        $this->line("  URL:       {$request->wdtk_full_url}");

        return self::SUCCESS;
    }
}
