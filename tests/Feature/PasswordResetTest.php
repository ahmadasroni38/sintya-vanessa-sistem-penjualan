<?php

namespace Tests\Feature;

use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run the migration for password reset OTPs
        $this->artisan('migrate');
    }

    /** @test */
    public function it_can_request_password_reset_otp()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Password reset OTP has been sent to your email.',
                ]);

        $this->assertDatabaseHas('password_reset_otps', [
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function it_fails_for_nonexistent_email()
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation errors',
                ]);
    }

    /** @test */
    public function it_can_verify_valid_otp()
    {
        $user = User::factory()->create();
        $otpRecord = PasswordResetOtp::generateForEmail($user->email);

        // Get the plain OTP for testing (normally this would be sent via email)
        $otp = $otpRecord->getPlainOtp();

        $response = $this->postJson('/api/auth/verify-otp', [
            'email' => $user->email,
            'otp' => $otp,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'OTP verified successfully.',
                ]);

        $this->assertDatabaseHas('password_reset_otps', [
            'id' => $otpRecord->id,
            'used_at' => now(),
        ]);
    }

    /** @test */
    public function it_fails_for_invalid_otp()
    {
        $user = User::factory()->create();
        PasswordResetOtp::generateForEmail($user->email);

        $response = $this->postJson('/api/auth/verify-otp', [
            'email' => $user->email,
            'otp' => '000000',
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid OTP. Please check and try again.',
                ]);
    }

    /** @test */
    public function it_fails_for_expired_otp()
    {
        $user = User::factory()->create();
        $otpRecord = PasswordResetOtp::generateForEmail($user->email);

        // Manually expire the OTP
        $otpRecord->update(['expires_at' => now()->subMinutes(1)]);

        $otp = $otpRecord->getPlainOtp();

        $response = $this->postJson('/api/auth/verify-otp', [
            'email' => $user->email,
            'otp' => $otp,
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid or expired OTP.',
                ]);
    }

    /** @test */
    public function it_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create();
        $otpRecord = PasswordResetOtp::generateForEmail($user->email);

        // Verify the OTP first
        $otp = $otpRecord->getPlainOtp();
        $otpRecord->verifyOtp($otp);

        $newPassword = 'NewPassword123!';

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => $otpRecord->id,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Password has been reset successfully.',
                ]);

        // Verify password was changed
        $user->refresh();
        $this->assertTrue(Hash::check($newPassword, $user->password));

        // Verify OTP was cleaned up
        $this->assertDatabaseMissing('password_reset_otps', [
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function it_fails_password_reset_with_weak_password()
    {
        $user = User::factory()->create();
        $otpRecord = PasswordResetOtp::generateForEmail($user->email);
        $otpRecord->verifyOtp($otpRecord->getPlainOtp());

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => $otpRecord->id,
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation errors',
                ]);
    }

    /** @test */
    public function it_fails_password_reset_with_mismatched_confirmation()
    {
        $user = User::factory()->create();
        $otpRecord = PasswordResetOtp::generateForEmail($user->email);
        $otpRecord->verifyOtp($otpRecord->getPlainOtp());

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => $otpRecord->id,
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation errors',
                ]);
    }

    /** @test */
    public function it_rate_limits_password_reset_requests_by_ip()
    {
        $user = User::factory()->create();

        // Make multiple requests to trigger rate limiting
        for ($i = 0; $i < 11; $i++) {
            $response = $this->postJson('/api/auth/forgot-password', [
                'email' => $user->email,
            ]);
        }

        $response->assertStatus(429)
                ->assertJson([
                    'success' => false,
                    'message' => 'Too many password reset attempts from this IP. Please try again later.',
                ]);
    }

    /** @test */
    public function it_rate_limits_password_reset_requests_by_email()
    {
        $user = User::factory()->create();

        // Make multiple requests to trigger rate limiting
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/forgot-password', [
                'email' => $user->email,
            ]);
        }

        $response->assertStatus(429)
                ->assertJson([
                    'success' => false,
                    'message' => 'Too many password reset attempts for this email. Please try again later.',
                ]);
    }

    /** @test */
    public function it_cleans_up_expired_otps_when_generating_new_ones()
    {
        $user = User::factory()->create();

        // Create an expired OTP
        $expiredOtp = PasswordResetOtp::generateForEmail($user->email);
        $expiredOtp->update(['expires_at' => now()->subMinutes(1)]);

        // Generate a new OTP
        $newOtp = PasswordResetOtp::generateForEmail($user->email);

        // Expired OTP should be deleted
        $this->assertDatabaseMissing('password_reset_otps', [
            'id' => $expiredOtp->id,
        ]);

        // New OTP should exist
        $this->assertDatabaseHas('password_reset_otps', [
            'id' => $newOtp->id,
        ]);
    }
}
