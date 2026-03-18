<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_see_only_their_links(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownLink = Link::query()->create([
            'user_id' => $user->id,
            'code' => 'own001',
            'original_url' => 'https://example.com/own',
        ]);

        $otherLink = Link::query()->create([
            'user_id' => $otherUser->id,
            'code' => 'other1',
            'original_url' => 'https://example.com/other',
        ]);

        $response = $this->actingAs($user)->get(route('links.index'));

        $response->assertOk();
        $response->assertSee($ownLink->code);
        $response->assertSee($ownLink->original_url);
        $response->assertDontSee($otherLink->code);
        $response->assertDontSee($otherLink->original_url);
    }

    public function test_authenticated_user_can_create_a_short_link(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('links.store'), [
            'original_url' => 'https://laravel.com/docs/12.x',
        ]);

        $response->assertRedirect(route('links.index', absolute: false));

        $this->assertDatabaseHas('links', [
            'user_id' => $user->id,
            'original_url' => 'https://laravel.com/docs/12.x',
        ]);
    }

    public function test_authenticated_user_can_update_their_own_link(): void
    {
        $user = User::factory()->create();

        $link = Link::query()->create([
            'user_id' => $user->id,
            'code' => 'edit01',
            'original_url' => 'https://example.com/old-url',
        ]);

        $response = $this->actingAs($user)->put(route('links.update', $link), [
            'original_url' => 'https://example.com/new-url',
        ]);

        $response->assertRedirect(route('links.index', absolute: false));

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'original_url' => 'https://example.com/new-url',
        ]);
    }

    public function test_authenticated_user_can_delete_their_own_link(): void
    {
        $user = User::factory()->create();

        $link = Link::query()->create([
            'user_id' => $user->id,
            'code' => 'del001',
            'original_url' => 'https://example.com/delete-me',
        ]);

        $response = $this->actingAs($user)->delete(route('links.destroy', $link));

        $response->assertRedirect(route('links.index', absolute: false));
        $this->assertSoftDeleted('links', ['id' => $link->id]);
    }

    public function test_user_cannot_edit_another_users_link(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherLink = Link::query()->create([
            'user_id' => $otherUser->id,
            'code' => 'forbid',
            'original_url' => 'https://example.com/private',
        ]);

        $response = $this->actingAs($user)->get(route('links.edit', $otherLink));

        $response->assertForbidden();
    }

    public function test_user_cannot_update_another_users_link(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherLink = Link::query()->create([
            'user_id' => $otherUser->id,
            'code' => 'forbup',
            'original_url' => 'https://example.com/private-update',
        ]);

        $response = $this->actingAs($user)->put(route('links.update', $otherLink), [
            'original_url' => 'https://example.com/attempted-update',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('links', [
            'id' => $otherLink->id,
            'original_url' => 'https://example.com/private-update',
        ]);
    }

    public function test_user_cannot_delete_another_users_link(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherLink = Link::query()->create([
            'user_id' => $otherUser->id,
            'code' => 'forbdel',
            'original_url' => 'https://example.com/private-delete',
        ]);

        $response = $this->actingAs($user)->delete(route('links.destroy', $otherLink));

        $response->assertForbidden();

        $this->assertDatabaseHas('links', [
            'id' => $otherLink->id,
            'deleted_at' => null,
        ]);
    }
}
