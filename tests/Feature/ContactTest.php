<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_valid_inquiry_is_stored_with_lowercase_email(): void
    {
        $this->post('/contact', [
            'name' => 'Ada',
            'email' => '  Ada@Example.COM ',
            'message' => 'Hello',
        ])->assertRedirect(route('contact'))->assertSessionHas('status');

        $this->assertDatabaseHas('inquiries', ['name' => 'Ada', 'email' => 'ada@example.com']);
    }

    public function test_invalid_inquiry_returns_errors(): void
    {
        $this->from('/contact')
            ->post('/contact', ['email' => 'nope'])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertSame(0, Inquiry::count());
    }

    public function test_honeypot_fakes_success_without_storing(): void
    {
        $this->post('/contact', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'spam',
            'website' => 'http://spam.test',
        ])->assertRedirect(route('contact'))->assertSessionHas('status');

        $this->assertSame(0, Inquiry::count());
    }
}
