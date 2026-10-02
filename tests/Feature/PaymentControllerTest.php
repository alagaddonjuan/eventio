<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_payment_form_requires_pending_rsvp_session()
    {
        // Without session
        $response = $this->get('/payment');
        
        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['error']);
    }

    public function test_payment_form_loads_with_session()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'price' => 5000]);

        $pendingRsvp = [
            'ticket_id' => $ticket->id,
            'quantity' => 1,
            'names' => ['John Doe'],
            'emails' => ['john@example.com'],
            'rsvp_status' => 'attending',
        ];

        $response = $this->withSession(['pending_rsvp_data' => $pendingRsvp])
                         ->get('/payment');
        
        $response->assertStatus(200);
        $response->assertViewIs('guest.payment');
    }
}
