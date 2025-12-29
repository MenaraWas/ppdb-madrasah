<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\StudentProfile;
use App\Domain\PPDB\Services\StatusTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class PublicPpdbController extends Controller
{
    public function home()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $waves = $activeYear
            ? $activeYear->waves()->with('tracks')->orderBy('sequence')->get()
            : collect();

        return view('ppdb.home', compact('activeYear', 'waves'));
    }

    public function initiate(Request $request, StatusTokenService $tokenService)
    {
        $data = $request->validate([
            'nisn' => 'required|string|max:20',
            'name' => 'required|string|max:120',
            'whatsapp' => 'required|string|max:30',
            'wave_id' => 'required|integer|exists:waves,id',
            'track_id' => 'required|integer|exists:tracks,id',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        // profile unik by tahun ajaran + NISN
        $profile = StudentProfile::firstOrCreate(
            ['academic_year_id' => $activeYear->id, 'nisn' => $data['nisn']],
            [
                'name' => $data['name'],
                'whatsapp' => $data['whatsapp'],
                'initiated_at' => now(),
                'last_activity_at' => now(),
                'initiated_ip' => $request->ip(),
            ]
        );

        // update ringan jika berbeda (optional)
        $profile->update([
            'name' => $data['name'],
            'whatsapp' => $data['whatsapp'],
            'last_activity_at' => now(),
        ]);

        // buat aplikasi unik per gelombang
        $application = Application::where('student_profile_id', $profile->id)
            ->where('wave_id', $data['wave_id'])
            ->first();

        if (!$application) {
            $rawToken = $tokenService->generateToken();

            $application = Application::create([
                'student_profile_id' => $profile->id,
                'wave_id' => $data['wave_id'],
                'track_id' => $data['track_id'],

                'status_utama' => 'INISIASI',
                'registered_at' => now(),

                'status_token_hash' => $tokenService->hashToken($rawToken),
                'token_created_at' => now(),
            ]);

            // redirect ke halaman status token
            return redirect()->route('ppdb.status', ['token' => $rawToken]);
        }

        // jika sudah ada aplikasi, kamu bisa arahkan ke status dengan recovery flow nanti.
        return back()->with('error', 'Anda sudah terdaftar pada gelombang ini. Silakan gunakan fitur Kirim Ulang Link Status.');
    }

    public function status(string $token, StatusTokenService $tokenService)
    {
        $hash = $tokenService->hashToken($token);

        $application = Application::with(['studentProfile', 'track', 'wave'])
            ->where('status_token_hash', $hash)
            ->firstOrFail();

        return view('ppdb.status', compact('application'));
    }

    public function uploadDocument(string $token, Request $request, StatusTokenService $tokenService)
    {
        $hash = $tokenService->hashToken($token);

        $application = Application::where('status_token_hash', $hash)->firstOrFail();

        $data = $request->validate([
            'doc_type' => 'required|string|max:50',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
        ]);

        $file = $data['file'];

        // simpan sementara
        $safeDocType = Str::upper(preg_replace('/[^A-Z0-9_]+/i', '_', $data['doc_type']));
        $tmpPath = $file->store("tmp_uploads/app_{$application->id}", 'local');

        // updateOrCreate per jenis dokumen (replace file lama)
        $doc = ApplicationDocument::updateOrCreate(
            ['application_id' => $application->id, 'doc_type' => $safeDocType],
            [
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size_bytes' => $file->getSize(),
                'checksum_sha256' => hash_file('sha256', Storage::disk('local')->path($tmpPath)),
                'upload_status' => 'MENUNGGU_UPLOAD_DRIVE',
                'temp_path' => $tmpPath,
                'error_message' => null,
                'retry_count' => 0,
            ]
        );

        // Dispatch job upload ke Drive (kita buat di step 13)
        \App\Jobs\UploadDocumentToDriveJob::dispatch($doc->id);

        return back()->with('success', "Dokumen {$safeDocType} diterima dan sedang diproses.");
    }


}
