<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_six_security_headers_are_present_in_http_response()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);

        // 1. Strict-Transport-Security (HSTS)
        $response->assertHeader('Strict-Transport-Security');

        // 2. Content-Security-Policy (CSP)
        $response->assertHeader('Content-Security-Policy');

        // 3. X-Frame-Options
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');

        // 4. X-Content-Type-Options
        $response->assertHeader('X-Content-Type-Options', 'nosniff');

        // 5. Referrer-Policy
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 6. Permissions-Policy
        $response->assertHeader('Permissions-Policy');
    }
}
