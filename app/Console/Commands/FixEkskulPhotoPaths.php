<?php

namespace App\Console\Commands;

use App\Models\JurnalEkskul;
use Illuminate\Console\Command;

class FixEkskulPhotoPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:ekskul-photo-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix ekskul photo paths by normalizing escaped backslashes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Jurnal Ekskul photo path fix...');
        
        $jurnals = JurnalEkskul::all();
        $count = 0;

        foreach ($jurnals as $jurnal) {
            $fotoPaths = is_array($jurnal->foto_kegiatan) ? $jurnal->foto_kegiatan : [];
            
            if (empty($fotoPaths)) {
                continue;
            }

            $fixed = false;
            $normalizedPaths = [];
            
            foreach ($fotoPaths as $path) {
                if ($path) {
                    // Normalize: replace escaped backslashes with forward slashes
                    $normalized = str_replace('\\', '/', $path);
                    if ($normalized !== $path) {
                        $fixed = true;
                    }
                    $normalizedPaths[] = $normalized;
                }
            }

            // Only update if paths were changed
            if ($fixed) {
                $jurnal->update(['foto_kegiatan' => $normalizedPaths]);
                $count++;
                $this->line("✓ Jurnal ID {$jurnal->id} ({$jurnal->tanggal}): Fixed " . count($normalizedPaths) . " paths");
            }
        }

        $this->info("✓ Path fix complete!");
        $this->info("Total records updated: $count");
        $this->info("Photo paths are now normalized - photos should display correctly!");

        return 0;
    }
}
