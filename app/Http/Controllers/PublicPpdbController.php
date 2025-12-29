<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\StudentProfile;
use App\Domain\PPDB\Services\StatusTokenService;
use Illuminate\Http\Request;

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

    


}
