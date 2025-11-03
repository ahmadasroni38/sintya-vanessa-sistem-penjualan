<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PasswordResetOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'used_at',
        'attempts',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Temporary storage for plain OTP (only in memory, never persisted)
     */
    protected $plainOtp = null;

    /**
     * Generate a new OTP for the given email
     */
    public static function generateForEmail(string $email, string $ipAddress = null, string $userAgent = null): self
    {
        // Clean up expired OTPs for this email
        self::where('email', $email)
            ->where('expires_at', '<', now())
            ->delete();

        // Generate 6-digit OTP
        $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $record = self::create([
            'email' => $email,
            'otp' => Hash::make($otp), // Hash the OTP for security
            'expires_at' => now()->addMinutes(15), // 15 minutes expiry
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        // Store plain OTP temporarily in memory only
        $record->plainOtp = $otp;

        return $record;
    }

    /**
     * Verify the OTP
     */
    public function verifyOtp(string $otp): bool
    {
        // Check if expired
        if ($this->isExpired()) {
            return false;
        }

        // Check if already used
        if ($this->used_at) {
            return false;
        }

        // Check attempts limit (max 5 attempts)
        if ($this->attempts >= 5) {
            return false;
        }

        // Verify OTP
        if (Hash::check($otp, $this->otp)) {
            $this->update(['used_at' => now()]);
            return true;
        }

        // Increment attempts
        $this->increment('attempts');
        return false;
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP is used
     */
    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }

    /**
     * Get the plain OTP (only available immediately after generation)
     */
    public function getPlainOtp(): ?string
    {
        // This is only available immediately after generateForEmail()
        // The plain OTP is never persisted to the database for security
        return $this->plainOtp;
    }

    /**
     * Scope for active OTPs
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now())
                    ->whereNull('used_at');
    }

    /**
     * Scope for expired OTPs
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}
