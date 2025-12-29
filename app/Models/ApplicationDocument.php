<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    protected $fillable = [
        'application_id',
        'doc_type',
        'original_filename',
        'mime_type',
        'file_size_bytes',
        'checksum_sha256',
        'upload_status',
        'temp_path',
        'drive_folder_id',
        'drive_file_id',
        'drive_file_name',
        'drive_web_view_link',
        'uploaded_to_drive_at',
        'retry_count',
        'error_message',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
