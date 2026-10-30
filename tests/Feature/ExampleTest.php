<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_guest_is_sent_to_the_teacher_login_page(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_teacher_login_page_is_available_in_indonesian(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Selamat datang, Bapak/Ibu Guru');
        $response->assertSee('Masuk');
    }
}
