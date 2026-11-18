<?php

namespace App\Console\Commands;

use App\Models\Medicine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncOushodhMedicines extends Command
{
    protected $signature = 'medicines:sync {--since=}';

    protected $description = 'Sync medicines from Oushodh API when OUSHODH_KEY is set';

    public function handle(): int
    {
        $key = config('services.oushodh.key');
        if (!$key) {
            $this->warn('OUSHODH_KEY not set. Skipping sync. Seed/import still works.');

            return self::SUCCESS;
        }

        $base = rtrim(config('services.oushodh.base'), '/');
        $cursor = null;
        $since = $this->option('since');
        $count = 0;

        do {
            $query = ['limit' => 100];
            if ($cursor) {
                $query['cursor'] = $cursor;
            }
            if ($since) {
                $query['updated_since'] = $since;
            }

            $response = Http::withToken($key)->get($base.'/v1/medicines', $query);
            if (!$response->successful()) {
                $this->error('Oushodh sync failed: '.$response->body());

                return self::FAILURE;
            }

            $payload = $response->json();
            foreach ($payload['items'] ?? [] as $item) {
                Medicine::updateOrCreate(
                    ['external_id' => (string) ($item['id'] ?? $item['uuid'] ?? '')],
                    [
                        'brand_name' => $item['brand'] ?? $item['product_name'] ?? 'Unknown',
                        'generic_name' => $item['generic_name'] ?? null,
                        'strength' => $item['strength'] ?? null,
                        'form' => $item['dosage_form'] ?? null,
                        'company' => $item['manufacturer'] ?? null,
                        'source' => 'oushodh',
                        'last_synced_at' => now(),
                    ]
                );
                $count++;
            }

            $cursor = $payload['next'] ?? null;
        } while ($cursor);

        $this->info("Synced {$count} medicines from Oushodh.");

        return self::SUCCESS;
    }
}
