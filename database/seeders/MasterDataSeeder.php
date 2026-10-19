<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterKelas;
use App\Models\MasterMapel;
use App\Models\MasterEkskul;
use App\Models\MasterTahunAjaran;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Master Kelas (idempotent)
        $kelas = [
            // Sample classes organized by program (Cambridge, ICT, Tahfidz)
            ['nama_kelas' => 'X-CAM-1', 'tingkat' => 'X', 'program' => 'Cambridge'],
            ['nama_kelas' => 'X-CAM-2', 'tingkat' => 'X', 'program' => 'Cambridge'],
            ['nama_kelas' => 'X-ICT-1', 'tingkat' => 'X', 'program' => 'ICT'],
            ['nama_kelas' => 'X-TAH-1', 'tingkat' => 'X', 'program' => 'Tahfidz'],
            ['nama_kelas' => 'XI-CAM-1', 'tingkat' => 'XI', 'program' => 'Cambridge'],
            ['nama_kelas' => 'XI-ICT-1', 'tingkat' => 'XI', 'program' => 'ICT'],
            ['nama_kelas' => 'XI-TAH-1', 'tingkat' => 'XI', 'program' => 'Tahfidz'],
            ['nama_kelas' => 'XII-CAM-1', 'tingkat' => 'XII', 'program' => 'Cambridge'],
            ['nama_kelas' => 'XII-ICT-1', 'tingkat' => 'XII', 'program' => 'ICT'],
            ['nama_kelas' => 'XII-TAH-1', 'tingkat' => 'XII', 'program' => 'Tahfidz'],
        ];

        foreach ($kelas as $data) {
            MasterKelas::updateOrCreate(['nama_kelas' => $data['nama_kelas']], $data);
        }

        // Master Mata Pelajaran (idempotent)
        $mapel = [
            ['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika', 'jenjang' => 'X-XII', 'program' => 'Semua'],
            ['kode_mapel' => 'BIO', 'nama_mapel' => 'Biologi', 'jenjang' => 'X-XII', 'program' => 'Semua'],
            ['kode_mapel' => 'FIS', 'nama_mapel' => 'Fisika', 'jenjang' => 'X-XII', 'program' => 'Semua'],
            ['kode_mapel' => 'KIM', 'nama_mapel' => 'Kimia', 'jenjang' => 'X-XII', 'program' => 'Semua'],
            ['kode_mapel' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'X-XII', 'program' => 'Semua'],
            ['kode_mapel' => 'BIG', 'nama_mapel' => 'Bahasa Inggris', 'jenjang' => 'X-XII', 'program' => 'Semua'],
            ['kode_mapel' => 'SEJ', 'nama_mapel' => 'Sejarah', 'jenjang' => 'X-XII', 'program' => 'Semua'],
            ['kode_mapel' => 'PKN', 'nama_mapel' => 'Pendidikan Kewarganegaraan', 'jenjang' => 'X-XII', 'program' => 'Semua'],
        ];

        foreach ($mapel as $data) {
            MasterMapel::updateOrCreate(['kode_mapel' => $data['kode_mapel']], $data);
        }

        // Master Ekstrakurikuler (23 items) - idempotent
        $eks_nama = ['Airsoft Gun', 'Akustik', 'Arabic Club', 'Astronomi', 'Badminton', 'Basket', 'Cooking Club', 'Desain Grafis', 'English Club', 'Fotografi', 'Futsal', 'IT Club', 'Jurnalistik', 'Karate', 'Manga', 'Panahan', 'Saman', 'Science Club', 'Taekwondo', 'Tapak Suci', 'Video Editing', 'Vocal Group', 'Voli'];
        foreach ($eks_nama as $name) {
            MasterEkskul::firstOrCreate(['nama_ekskul' => $name]);
        }

        // Master Tahun Ajaran (only one)
        MasterTahunAjaran::updateOrCreate(['tahun_ajaran' => '2024/2025'], ['is_active' => true]);
    }
}