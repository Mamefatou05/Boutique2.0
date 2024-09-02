<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Client;
use App\Models\User;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_filter_clients_by_telephone()
    {
        $client1 = Client::factory()->create(['telephone' => '774789089']);
        $client2 = Client::factory()->create(['telephone' => '774123456']);

        $response = $this->get('/wane/v1/clients?telephone=774789089');

        $response->assertStatus(200)
                 ->assertJsonFragment(['telephone' => '774789089'])
                 ->assertJsonMissing(['telephone' => '774123456']);
    }

    /** @test */
    public function it_can_sort_clients_by_created_at()
    {
        $client1 = Client::factory()->create(['created_at' => now()->subDays(1)]);
        $client2 = Client::factory()->create(['created_at' => now()]);

        $response = $this->get('/wane/v1/clients?sort=created_at');

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $client2->id])
                 ->assertJsonMissing(['id' => $client1->id]);
    }

    /** @test */
    public function it_can_include_user_relation()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $response = $this->get('/wane/v1/clients/' . $client->id . '?include=users');

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $user->id])
                 ->assertJsonFragment(['user_id' => $client->user_id]);
    }
}
