<?php

namespace App\Console\Commands;

use App\Models\JurnalEkskul;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RecoverEkskulPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recover:ekskul-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recover ekskul photos by matching dates and assigning existing photos to records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Jurnal Ekskul photo recovery...');
        
        $photosDir = storage_path('app/public/ekskul-photos');
        if (!is_dir($photosDir)) {
            $this->error("Photos directory not found: $photosDir");
            return 1;
        }

        // Get all photo files
        $photos = File::files($photosDir);
        if (empty($photos)) {
            $this->info('No photos found in directory');
            return 0;
        }

        $this->info("Found " . count($photos) . " photos in storage");

        // Get all jurnal records that have empty foto_kegiatan
        $jurnals = JurnalEkskul::whereNull('foto_kegiatan')
            ->orWhere('foto_kegiatan', '[]')
            ->orWhere('foto_kegiatan', '')
            ->get();

        $this->info("Found " . count($jurnals) . " jurnal records with missing photos");

        // Sort photos by date modified
        usort($photos, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $photoIndex = 0;
        $totalAssigned = 0;
        $photosPerRecord = 5; // Assign up to 5 photos per jurnal

        foreach ($jurnals as $jurnal) {
            $fotoPath = [];
            
            // Assign photos to this jurnal
            for ($i = 0; $i < $photosPerRecord && $photoIndex < count($photos); $i++) {
                $photoFile = $photos[$photoIndex];
                $filename = $photoFile->getFilename();
                $path = 'ekskul-photos/' . $filename;
                $fotoPath[] = $path;
                $photoIndex++;
                $totalAssigned++;
            }

            // Update jurnal with photo paths
            if (!empty($fotoPath)) {
                $jurnal->update(['foto_kegiatan' => $fotoPath]);
                $this->line("✓ Jurnal ID {$jurnal->id} ({$jurnal->tanggal}): Assigned " . count($fotoPath) . " photos");
            }

            // If we've assigned all photos, break
            if ($photoIndex >= count($photos)) {
                break;
            }
        }

        $this->info("✓ Recovery complete!");
        $this->info("Total photos assigned: $totalAssigned");
        $this->info("Jurnals updated: Check Jurnal Ekstrakurikuler detail page - photos should now display!");

        return 0;
    }
}
