<?php

namespace Tests\Feature;

use App\Post;
use App\Vote;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * @testdox Posts\Get
 * @group posts
 * @group posts-get
 */
class PostsGetTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function can_get_post_by_id()
    {
        $post = factory(Post::class)->create();

        $this->json('GET', 'api/posts/' . $post->id)
            ->assertStatus(200)
            ->assertJsonStructure([
                'id', 'image', 'description', 'user_id', 'created_at', 'updated_at',
            ]);
    }

    /**
     * @test
     */
    public function can_get_post_votes()
    {
        $vote = factory(Vote::class)->create();

        $this->json('GET', 'api/posts/' . $vote->voted_id . '/votes')
            ->assertStatus(200)
            ->assertJsonCount(1);
    }

    /**
     * @test
     */
    public function post_not_found()
    {
        $this->json('GET', 'api/posts/' . 999)
            ->assertStatus(404);
    }

}