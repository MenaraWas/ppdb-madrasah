<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Wave;
use App\Models\Track;
use App\Models\TrackPublication;
use Illuminate\Support\Carbon;

class PpdbMasterSeeder extends Seeder
{
    public function run(): void
    {
        // === Tahun ajaran aktif (contoh: 2026/2027) ===
        $yearName = '2026/2027';

        $year = AcademicYear::updateOrCreate(
            ['name' => $yearName],
            [
                'start_date' => '2026-07-01',
                'end_date'   => '2027-06-30',
                'is_active'  => true,
                'archived_at' => null,
            ]
        );

        // Matikan tahun ajaran lain (biar cuma satu aktif)
        AcademicYear::where('id', '!=', $year->id)->update(['is_active' => false]);

        // === Gelombang 1 ===
        $wave1 = Wave::updateOrCreate(
            [
                'academic_year_id' => $year->id,
                'sequence' => 1,
            ],
            [
                'name' => 'Gelombang 1',
                'status' => 'DIBUKA',
                // boleh kamu atur tanggalnya, kalau belum pasti, biarkan null
                'opens_at' => Carbon::now()->subDay(),
                'closes_at' => Carbon::now()->addDays(30),
            ]
        );

        // === Jalur (Tracks) ===
        $tracksData = [
            [
                'code' => 'PRESTASI',
                'name' => 'Jalur Prestasi',
                'requires_interview' => true,
                'quota' => null,            // admin isi nanti
                'reserve_enabled' => true,
                'reserve_count' => 0,        // admin isi nanti
            ],
            [
                'code' => 'TAHFIDZ',
                'name' => 'Jalur Tahfidz',
                'requires_interview' => true,
                'quota' => null,
                'reserve_enabled' => true,
                'reserve_count' => 0,
            ],
            [
                'code' => 'REGULER',
                'name' => 'Jalur Reguler',
                'requires_interview' => true, // kalau reguler mau cepat tanpa wawancara, set false
                'quota' => null,
                'reserve_enabled' => true,
                'reserve_count' => 0,
            ],
        ];

        foreach ($tracksData as $t) {
            $track = Track::updateOrCreate(
                [
                    'wave_id' => $wave1->id,
                    'code' => $t['code'],
                ],
                [
                    'name' => $t['name'],
                    'requires_interview' => $t['requires_interview'],
                    'quota' => $t['quota'],
                    'reserve_enabled' => $t['reserve_enabled'],
                    'reserve_count' => $t['reserve_count'],
                    'is_active' => true,
                ]
            );

            // Pastikan track_publications ada (1 per track)
            TrackPublication::updateOrCreate(
                ['track_id' => $track->id],
                [
                    'state' => 'BELUM_DIPUBLISH',
                    'is_published' => false,
                    'published_at' => null,
                    'ever_published_at' => null,
                    'locked_final_at' => null,
                    'locked_by_user_id' => null,
                ]
            );
        }
    }
}
