<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_category()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson('/api/categories', [
                'title' => 'Music',
            ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Category created successfully',
            ]);

        $this->assertDatabaseHas('categories', [
            'title' => 'Music',
        ]);
    }

    public function test_user_cannot_create_category()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/categories', [
                'title' => 'Sports',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_category()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::factory()->create([
            'title' => 'Old Title',
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->putJson("/api/categories/{$category->id}", [
                'title' => 'New Title',
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Category updated successfully',
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'title' => 'New Title',
        ]);
    }

    public function test_user_cannot_update_category()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $category = Category::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson("/api/categories/{$category->id}", [
                'title' => 'Hacked Title',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_category()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::factory()->create();

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->deleteJson("/api/categories/{$category->id}");

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Category Deleted Successfully',
            ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_user_cannot_delete_category()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $category = Category::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(403);
    }
}
