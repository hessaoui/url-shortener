<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_known_short_code_redirects_to_original_url_and_tracks_usage(): void
    {
        $user = User::factory()->create();

        $link = Link::query()->create([
            'user_id' => $user->id,
            'code' => 'go1234',
            'original_url' => 'https://www.example.org/some/path',
        ]);

        $response = $this->get(route('redirect.show', $link->code));

        $response->assertRedirect('https://www.example.org/some/path');

        $link->refresh();

        $this->assertSame(1, (int) $link->clicks);
        $this->assertNotNull($link->last_used_at);
    }

    public function test_unknown_short_code_returns_404(): void
    {
        $response = $this->get(route('redirect.show', 'unknown'));

        $response->assertNotFound();
    }

    public function test_deleted_short_code_shows_deleted_page(): void
    {
        $user = User::factory()->create();

        $link = Link::query()->create([
            'user_id' => $user->id,
            'code' => 'gone01',
            'original_url' => 'https://example.com/will-be-deleted',
        ]);

        $link->delete();

        $response = $this->get(route('redirect.show', $link->code));

        $response->assertOk();
        $response->assertSee('no longer valid', false);

        $link->refresh();

        $this->assertSame(0, (int) $link->clicks);
        $this->assertNull($link->last_used_at);
    }
}
