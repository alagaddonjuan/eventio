<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;

class EventControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_view_event_dashboard()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    public function test_user_can_create_event()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/events', [
            'title' => 'Test Event',
            'start_date' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'end_date' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'venue_name' => 'Test Venue',
            'latitude' => '10.0',
            'longitude' => '20.0',
            'payment_status' => 'free',
            'privacy_type' => 'public',
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('events', [
            'title' => 'Test Event',
            'venue_name' => 'Test Venue',
        ]);
    }
}
