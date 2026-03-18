<?php

namespace App\Console\Commands;

use App\Models\Link;
use Illuminate\Console\Command;

class PruneStaleLinks extends Command
{
    protected $signature = 'links:prune-stale';

    protected $description = 'Soft delete links that have not been used for at least three months';

    public function handle(): int
    {
        $cutoff = now()->subMonths(3);

        $deletedCount = Link::query()
            ->whereNull('deleted_at')
            ->where(function ($query) use ($cutoff) {
                $query->where('last_used_at', '<=', $cutoff)
                    ->orWhere(function ($query) use ($cutoff) {
                        $query->whereNull('last_used_at')
                            ->where('created_at', '<=', $cutoff);
                    });
            })
            ->delete();

        $this->info("{$deletedCount} stale links pruned.");

        return self::SUCCESS;
    }
}