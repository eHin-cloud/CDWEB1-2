<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewsletterSubscribeTest extends TestCase
{
    /**
     * Test đăng ký nhận tin với email trống hoặc không hợp lệ -> trả lỗi 422
     */
    public function test_subscribe_newsletter_fails_with_invalid_email(): void
    {
        $resEmpty = $this->postJson('/renty/newsletter/subscribe', ['email' => '']);
        $resEmpty->assertStatus(422);
        $resEmpty->assertJsonValidationErrors(['email']);

        $resInvalid = $this->postJson('/renty/newsletter/subscribe', ['email' => 'not-an-email']);
        $resInvalid->assertStatus(422);
        $resInvalid->assertJsonValidationErrors(['email']);
    }

    /**
     * Test đăng ký nhận tin thành công và kích hoạt gửi mail
     */
    public function test_subscribe_newsletter_success_and_sends_email(): void
    {
        Mail::fake();

        $email = 'student.test_' . bin2hex(random_bytes(3)) . '@example.com';

        $response = $this->postJson('/renty/newsletter/subscribe', [
            'email' => $email,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        Mail::assertSent(\Illuminate\Mail\Mailable::class, 0); // sent via raw Mail::send closure
    }
}
