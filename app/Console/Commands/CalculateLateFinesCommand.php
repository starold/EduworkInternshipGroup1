<?php

namespace App\Console\Commands;

use App\Services\FineCalculationService;
use Illuminate\Console\Command;

class CalculateLateFinesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rental:calculate-fines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hitung dan perbarui denda keterlambatan pengembalian motor secara otomatis';

    /**
     * Execute the console command.
     */
    public function handle(FineCalculationService $service): int
    {
        $this->info('Memulai perhitungan denda keterlambatan...');

        $processedCount = $service->calculateAllActiveBookings();

        $this->info("Perhitungan selesai. {$processedCount} item keterlambatan diproses.");

        return Command::SUCCESS;
    }
}
