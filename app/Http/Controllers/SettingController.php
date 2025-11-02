<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Display the system settings.
     */
    public function index()
    {
        try {
            // Get the first setting record, or create default if none exists
            $setting = Setting::first();

            if (!$setting) {
                $setting = Setting::create([
                    'nama_sistem' => 'Sistem Manajemen',
                    'deskripsi_sistem' => 'Deskripsi sistem default',
                    'nama_perusahaan' => 'Nama Perusahaan',
                    'alamat_lengkap' => 'Alamat lengkap perusahaan',
                    'email_perusahaan' => 'email@perusahaan.com',
                    'nomor_telepon' => '081234567890',
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $setting
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil pengaturan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        try {
            // Custom validation messages in Indonesian
            $messages = [
                'nama_sistem.required' => 'Nama sistem wajib diisi',
                'nama_sistem.max' => 'Nama sistem maksimal 255 karakter',
                'deskripsi_sistem.required' => 'Deskripsi sistem wajib diisi',
                'nama_perusahaan.required' => 'Nama perusahaan wajib diisi',
                'nama_perusahaan.max' => 'Nama perusahaan maksimal 255 karakter',
                'alamat_lengkap.required' => 'Alamat lengkap wajib diisi',
                'email_perusahaan.required' => 'Email perusahaan wajib diisi',
                'email_perusahaan.email' => 'Format email tidak valid',
                'nomor_telepon.required' => 'Nomor telepon wajib diisi',
                'logo_sistem.image' => 'Logo sistem harus berupa file gambar',
                'logo_sistem.mimes' => 'Logo sistem harus berformat jpeg, png, jpg, atau gif',
                'logo_sistem.max' => 'Ukuran logo sistem maksimal 2MB',
            ];

            // Debug: Log all request data
            Log::info('Settings update request data:', $request->all());
            Log::info('Settings update request files:', $request->allFiles());

            // Validate the request - use 'filled' instead of 'required' for better FormData handling
            $validated = $request->validate([
                'nama_sistem' => 'required|string|max:255',
                'deskripsi_sistem' => 'required|string',
                'nama_perusahaan' => 'required|string|max:255',
                'alamat_lengkap' => 'required|string',
                'email_perusahaan' => 'required|email',
                'nomor_telepon' => 'required|string|max:20',
                'footer_text' => 'nullable|string',
                'logo_sistem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], $messages);

            Log::info('Validation passed, validated data:', $validated);

            DB::beginTransaction();

            // Get or create the setting record
            $setting = Setting::first();
            if (!$setting) {
                $setting = new Setting();
            }

            // Handle logo upload
            if ($request->hasFile('logo_sistem')) {
                // Delete old logo if exists
                if ($setting->logo_sistem && Storage::disk('public')->exists('logo/' . $setting->logo_sistem)) {
                    Storage::disk('public')->delete('logo/' . $setting->logo_sistem);
                }

                // Store new logo
                $logoFile = $request->file('logo_sistem');
                $logoName = time() . '_' . uniqid() . '.' . $logoFile->getClientOriginalExtension();
                $logoFile->storeAs('logo', $logoName, 'public');
                $validated['logo_sistem'] = $logoName;
            } else {
                // Keep existing logo if no new file uploaded
                unset($validated['logo_sistem']);
            }

            // Update the setting
            $setting->fill($validated);
            $setting->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan sistem berhasil diperbarui',
                'data' => $setting
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui pengaturan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
