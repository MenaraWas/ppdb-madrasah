<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Wajib: reset cache permission spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /**
         * PERMISSIONS
         * Gunakan penamaan "namespace.action" biar konsisten & mudah dicari
         */
        $permissions = [
            // ===== Master / Setup PPDB =====
            'ppdb.manage.tahun_ajaran',
            'ppdb.manage.gelombang',
            'ppdb.manage.jalur',
            'ppdb.manage.pengaturan_ppdb',

            // ===== Konten Home (Banner & Dokumen publik) =====
            'ppdb.content.view',
            'ppdb.content.manage',

            // ===== Data Pendaftar / Aplikasi =====
            'ppdb.applications.view',
            'ppdb.applications.update',
            'ppdb.applications.delete',

            // ===== Berkas Fisik =====
            'ppdb.physical.view',
            'ppdb.physical.receive',
            'ppdb.physical.release',

            // ===== Verifikasi Berkas =====
            'ppdb.verify.view',
            'ppdb.verify.process',

            // ===== Wawancara =====
            'ppdb.interview.view',
            'ppdb.interview.schedule',
            'ppdb.interview.score',
            'ppdb.interview.reschedule',

            // ===== Penilaian & Ranking =====
            'ppdb.scoring.manage',       // bobot/rubrik/tie-breaker (sebelum publish)
            'ppdb.ranking.recalculate',
            'ppdb.ranking.publish',
            'ppdb.ranking.unpublish',
            'ppdb.ranking.lock_final',
            'ppdb.ranking.unlock_final', // super admin saja

            // ===== Hasil, Daftar Ulang, Cadangan =====
            'ppdb.final.set_result',
            'ppdb.enrollment.manage',
            'ppdb.waitlist.promote',

            // ===== Gugur & Withdraw =====
            'ppdb.drop.process',
            'ppdb.withdraw.process',

            // ===== User Internal =====
            'system.users.manage',

            // ===== Audit & Export =====
            'system.audit.view',
            'system.export.run',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /**
         * ROLES
         */
        $roles = [
            'super_admin',
            'admin',
            'verifier',
            'loket',
            'pewawancara',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        /**
         * ROLE -> PERMISSIONS
         */

        // Super Admin: semua permission
        Role::findByName('super_admin')
            ->syncPermissions(Permission::all());

        // Admin: hampir semua kecuali unlock final (khusus super admin)
        $adminPermissions = Permission::whereNotIn('name', [
            'ppdb.ranking.unlock_final',
        ])->get();

        Role::findByName('admin')
            ->syncPermissions($adminPermissions);

        // Verifier: verifikasi berkas + lihat pendaftar + lihat status fisik
        Role::findByName('verifier')
            ->syncPermissions([
                'ppdb.applications.view',
                'ppdb.physical.view',
                'ppdb.verify.view',
                'ppdb.verify.process',
                'ppdb.content.view',
            ]);

        // Loket: lihat pendaftar + update data seperlunya + terima berkas fisik
        Role::findByName('loket')
            ->syncPermissions([
                'ppdb.applications.view',
                'ppdb.applications.update',
                'ppdb.physical.view',
                'ppdb.physical.receive',
                'ppdb.content.view',
                // opsional kalau loket boleh cabut berkas fisik:
                // 'ppdb.physical.release',
            ]);

        // Pewawancara: hanya lihat wawancara + input skor
        Role::findByName('pewawancara')
            ->syncPermissions([
                'ppdb.interview.view',
                'ppdb.interview.score',
                'ppdb.content.view',
            ]);
    }
}
