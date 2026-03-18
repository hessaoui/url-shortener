<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PruneStaleLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_soft_deletes_links_not_used_for_more_than_three_months(): void
    {
        $user = User::factory()->create();

        $staleUsedLink = Link::forceCreate([
            'user_id' => $user->id,
            'code' => 'stale1',
            'original_url' => 'https://example.com/stale-used',
            'last_used_at' => now()->subMonths(4),
            'created_at' => now()->subMonths(5),
            'updated_at' => now()->subMonths(4),
        ]);

        $staleNeverUsedLink = Link::forceCreate([
            'user_id' => $user->id,
            'code' => 'stale2',
            'original_url' => 'https://example.com/stale-never-used',
            'last_used_at' => null,
            'created_at' => now()->subMonths(4),
            'updated_at' => now()->subMonths(4),
        ]);

        $recentLink = Link::forceCreate([
            'user_id' => $user->id,
            'code' => 'fresh1',
            'original_url' => 'https://example.com/recent',
            'last_used_at' => now()->subDays(10),
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        $this->artisan('links:prune-stale')
            ->expectsOutput('2 stale links pruned.')
            ->assertSuccessful();

        $this->assertSoftDeleted('links', ['id' => $staleUsedLink->id]);
        $this->assertSoftDeleted('links', ['id' => $staleNeverUsedLink->id]);
        $this->assertDatabaseHas('links', [
            'id' => $recentLink->id,
            'deleted_at' => null,
        ]);
    }

    public function test_command_does_not_prune_recently_created_unused_links(): void
    {
        $user = User::factory()->create();

        $recentUnusedLink = Link::forceCreate([
            'user_id' => $user->id,
            'code' => 'fresh2',
            'original_url' => 'https://example.com/recent-unused',
            'last_used_at' => null,
            'created_at' => now()->subMonth(),
            'updated_at' => now()->subMonth(),
        ]);

        $this->artisan('links:prune-stale')
            ->expectsOutput('0 stale links pruned.')
            ->assertSuccessful();

        $this->assertDatabaseHas('links', [
            'id' => $recentUnusedLink->id,
            'deleted_at' => null,
        ]);
    }
}