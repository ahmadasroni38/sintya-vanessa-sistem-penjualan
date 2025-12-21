<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo_sistem',
        'nama_sistem',
        'deskripsi_sistem',
        'nama_perusahaan',
        'alamat_lengkap',
        'email_perusahaan',
        'nomor_telepon',
        'footer_text',
        'report_checker_name',
        'report_approver_name',
    ];
}
