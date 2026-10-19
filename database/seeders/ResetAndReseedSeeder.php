<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MasterEkskul;
use App\Models\MasterMapel;
use App\Models\MasterKelas;
use App\Models\Siswa;
use App\Models\SiswaEkstrakurikuler;

class ResetAndReseedSeeder extends Seeder
{
    public function run(): void
    {
        // WARNING: destructive operation -- truncates many tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'jurnal_kbm',
            'jurnal_ekskul',
            'jurnal_kegiatan',
            'jurnal_piket',
            'siswa_ekstrakurikuler',
            'siswas',
            'master_mapels',
            'master_ekskul',
            'master_kelas',
        ];

        foreach ($tables as $t) {
            if (DB::getSchemaBuilder()->hasTable($t)) {
                DB::table($t)->truncate();
            }
        }

        // Seed Master Ekskul (23 canonical)
        $eks_nama = ['Airsoft Gun', 'Akustik', 'Arabic Club', 'Astronomi', 'Badminton', 'Basket', 'Cooking Club', 'Desain Grafis', 'English Club', 'Fotografi', 'Futsal', 'IT Club', 'Jurnalistik', 'Karate', 'Manga', 'Panahan', 'Saman', 'Science Club', 'Taekwondo', 'Tapak Suci', 'Video Editing', 'Vocal Group', 'Voli'];
        foreach ($eks_nama as $name) {
            MasterEkskul::create(['nama_ekskul' => $name]);
        }

        // Seed Master Mapels per program
        $programMapels = [
            'Cambridge' => [
                ['kode' => 'CAM-MTK', 'nama' => 'Matematika'],
                ['kode' => 'CAM-FIS', 'nama' => 'Fisika'],
                ['kode' => 'CAM-KIM', 'nama' => 'Kimia'],
                ['kode' => 'CAM-BIG', 'nama' => 'Bahasa Inggris'],
                ['kode' => 'CAM-BIO', 'nama' => 'Biologi'],
            ],
            'ICT' => [
                ['kode' => 'ICT-MTK', 'nama' => 'Matematika'],
                ['kode' => 'ICT-INF', 'nama' => 'Informatika'],
                ['kode' => 'ICT-KR', 'nama' => 'Komputer & Jaringan'],
                ['kode' => 'ICT-DG', 'nama' => 'Desain Grafis'],
                ['kode' => 'ICT-BIG', 'nama' => 'Bahasa Inggris'],
            ],
            'Tahfidz' => [
                ['kode' => 'TAH-PAI', 'nama' => 'Pendidikan Agama Islam'],
                ['kode' => 'TAH-TAH', 'nama' => 'Tahfidz'],
                ['kode' => 'TAH-AR', 'nama' => 'Bahasa Arab'],
                ['kode' => 'TAH-AD', 'nama' => 'Akhlak & Dakwah'],
                ['kode' => 'TAH-BIG', 'nama' => 'Bahasa Inggris'],
            ],
            // common subjects
            'Semua' => [
                ['kode' => 'PKN', 'nama' => 'Pendidikan Kewarganegaraan'],
                ['kode' => 'SEJ', 'nama' => 'Sejarah'],
            ],
        ];

        foreach ($programMapels as $program => $items) {
            foreach ($items as $it) {
                MasterMapel::create([
                    'kode_mapel' => $it['kode'],
                    'nama_mapel' => $it['nama'],
                    'jenjang' => 'X-XII',
                    'program' => $program,
                ]);
            }
        }

        // Seed Master Kelas (35 classes) -- distribute across programs and levels
        $programs = ['Cambridge', 'ICT', 'Tahfidz'];
        $tingkat = ['X', 'XI', 'XII'];

        $classesToCreate = 35;
        $created = 0;
        $counter = 1;
        while ($created < $classesToCreate) {
            foreach ($tingkat as $t) {
                foreach ($programs as $p) {
                    if ($created >= $classesToCreate) break 2;
                    $name = "{$t}-" . strtoupper(substr($p,0,3)) . "-" . ceil($counter);
                    MasterKelas::create([
                        'nama_kelas' => $name,
                        'tingkat' => $t,
                        'jurusan' => 'IPA',
                        'program' => $p,
                        'kapasitas' => 32,
                    ]);
                    $created++;
                    $counter++;
                }
            }
        }

        // Create 2 siswa per kelas (so 70 siswa for 35 classes) with unique NIS
        $kelasList = MasterKelas::all();
        $nisCounter = 1000001;
        foreach ($kelasList as $kelas) {
            for ($i = 1; $i <= 2; $i++) {
                Siswa::create([
                    'kelas_id' => $kelas->id,
                    'nama_siswa' => "Siswa {$kelas->nama_kelas} - {$i}",
                    'jenis_kelamin' => ($i % 2 == 0) ? 'P' : 'L',
                    'nis' => (string) $nisCounter,
                ]);
                $nisCounter++;
            }
        }

        // Assign some students to ekstrakurikuler groups (so absensi shows participants)
        $eks = MasterEkskul::all();
        $siswaAll = Siswa::all();
        $sCount = $siswaAll->count();
        $sIndex = 0;

        foreach ($eks as $e) {
            // take up to 5 students in round-robin fashion
            for ($j = 0; $j < 5; $j++) {
                $s = $siswaAll->get($sIndex % $sCount);
                if (!$s) break;
                SiswaEkstrakurikuler::create(['ekstrakurikuler_id' => $e->id, 'siswa_id' => $s->id]);
                $sIndex++;
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Done
    }
}
