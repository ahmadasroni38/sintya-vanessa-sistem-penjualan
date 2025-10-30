<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_entry_id',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
        'file_path',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Get the journal entry that owns the attachment.
     */
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Get the user who uploaded the attachment.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the file extension.
     */
    public function getFileExtensionAttribute(): string
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    /**
     * Check if the file is an image.
     */
    public function isImage(): bool
    {
        return in_array(strtolower($this->getFileExtensionAttribute()), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
    }

    /**
     * Check if the file is a PDF.
     */
    public function isPdf(): bool
    {
        return strtolower($this->getFileExtensionAttribute()) === 'pdf';
    }

    /**
     * Check if the file is a document.
     */
    public function isDocument(): bool
    {
        $documentExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'];
        return in_array(strtolower($this->getFileExtensionAttribute()), $documentExtensions);
    }

    /**
     * Get the file URL.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get the download URL.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('journal-entries.download-attachment', $this->id);
    }

    /**
     * Scope a query to only include images.
     */
    public function scopeImages($query)
    {
        return $query->whereRaw("LOWER(SUBSTRING_INDEX(filename, '.', -1)) IN ?",
            ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
    }

    /**
     * Scope a query to only include PDFs.
     */
    public function scopePdfs($query)
    {
        return $query->whereRaw("LOWER(SUBSTRING_INDEX(filename, '.', -1)) = ?", ['pdf']);
    }

    /**
     * Scope a query to only include documents.
     */
    public function scopeDocuments($query)
    {
        return $query->whereRaw("LOWER(SUBSTRING_INDEX(filename, '.', -1)) IN ?",
            ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf']);
    }
}
