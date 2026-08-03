<?php

namespace App\Console\Commands;

use App\Models\DomainRenewal;
use App\Models\SoftwareRenewal;
use Illuminate\Console\Command;

class AutoUpdateRenewalStatus extends Command
{
    protected $signature = 'renewal:auto-status';

    protected $description = 'Auto-update domain & software renewal status based on expiration dates';

    public function handle(): int
    {
        $domainUpdated = 0;
        $softwareUpdated = 0;

        DomainRenewal::where(function ($query) {
                $query->whereNull('status')
                    ->orWhereNotIn('status', ['Renewed', 'Cancelled']);
            })
            ->each(function ($record) use (&$domainUpdated) {
                $oldStatus = $record->status;
                $record->syncStatus();
                if ($oldStatus !== $record->fresh()->status) {
                    $domainUpdated++;
                }
            });

        SoftwareRenewal::where(function ($query) {
                $query->whereNull('status')
                    ->orWhereNotIn('status', ['Renewed', 'Cancelled']);
            })
            ->each(function ($record) use (&$softwareUpdated) {
                $oldStatus = $record->status;
                $record->syncStatus();
                if ($oldStatus !== $record->fresh()->status) {
                    $softwareUpdated++;
                }
            });

        $this->info("Domain renewals updated: {$domainUpdated}");
        $this->info("Software renewals updated: {$softwareUpdated}");

        return Command::SUCCESS;
    }
}
