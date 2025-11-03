<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Update user profile.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'address' => $data['address'] ?? null,
        ];

        // Handle avatar upload if provided
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $updateData['avatar_url'] = $this->handleAvatarUpload($user, $data['avatar']);
        }

        $user->update($updateData);

        return $user->fresh();
    }

    /**
     * Handle avatar upload and optimization.
     *
     * @param User $user
     * @param UploadedFile $avatar
     * @return string
     */
    protected function handleAvatarUpload(User $user, UploadedFile $avatar): string
    {
        // Delete old avatar if exists
        if ($user->avatar_url) {
            $this->deleteOldAvatar($user->avatar_url);
        }

        // Generate unique filename
        $filename = 'avatar_' . $user->id . '_' . time() . '.jpg';
        $path = 'avatars/' . $filename;

        // Optimize and resize image using GD library
        $optimizedImage = $this->optimizeImage($avatar->getRealPath(), 500, 500, 80);

        // Store the optimized image
        Storage::disk('public')->put($path, $optimizedImage);

        return Storage::url($path);
    }

    /**
     * Optimize image using GD library.
     *
     * @param string $sourcePath
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $quality
     * @return string
     */
    protected function optimizeImage(string $sourcePath, int $maxWidth, int $maxHeight, int $quality): string
    {
        // Get image info
        list($width, $height, $type) = getimagesize($sourcePath);

        // Create image resource from file
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new \Exception('Unsupported image type');
        }

        // Calculate new dimensions (maintain aspect ratio and fit within max dimensions)
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        // Create new image
        $destination = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG
        imagealphablending($destination, false);
        imagesavealpha($destination, true);

        // Resize
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Start output buffering
        ob_start();
        imagejpeg($destination, null, $quality);
        $imageData = ob_get_clean();

        // Free memory
        imagedestroy($source);
        imagedestroy($destination);

        return $imageData;
    }

    /**
     * Delete old avatar file.
     *
     * @param string $avatarUrl
     * @return void
     */
    protected function deleteOldAvatar(string $avatarUrl): void
    {
        try {
            // Extract path from URL
            $path = str_replace('/storage/', '', parse_url($avatarUrl, PHP_URL_PATH));

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Exception $e) {
            // Log error but don't throw exception
            \Log::warning("Failed to delete old avatar: " . $e->getMessage());
        }
    }

    /**
     * Update user password.
     *
     * @param User $user
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     * @throws \Exception
     */
    public function updatePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        // Verify current password
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        // Update password
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return true;
    }

    /**
     * Get user profile data.
     *
     * @param User $user
     * @return array
     */
    public function getProfile(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'date_of_birth' => $user->date_of_birth,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
            'avatar_url' => $user->avatar_url,
            'status' => $user->status,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'active_role' => $user->activeRole(),
        ];
    }
}
