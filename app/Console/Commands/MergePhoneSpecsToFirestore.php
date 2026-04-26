<?php

namespace App\Console\Commands;

use App\Services\Firestore\FirestoreClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MergePhoneSpecsToFirestore extends Command
{
    protected $signature = 'firestore:merge-phone-specs';

    protected $description = 'Merge embedded specs from SQLite into existing Firestore phone documents';

    public function handle(FirestoreClient $client)
    {
        $phones = DB::table('phones')->get();
        $bar = $this->output->createProgressBar(count($phones));
        $bar->start();
        $merged = 0;
        $skipped = 0;

        foreach ($phones as $phone) {
            $existing = $client->getDocument('phones', (string) $phone->id);

            if (! $existing) {
                $skipped++;
                $bar->advance();

                continue;
            }

            $data = [];

            $body = DB::table('spec_bodies')->where('phone_id', $phone->id)->first();
            if ($body) {
                $data['body'] = (array) $body;
                unset($data['body']['id'], $data['body']['phone_id'], $data['body']['created_at'], $data['body']['updated_at']);
            }

            $platform = DB::table('spec_platforms')->where('phone_id', $phone->id)->first();
            if ($platform) {
                $data['platform'] = (array) $platform;
                unset($data['platform']['id'], $data['platform']['phone_id'], $data['platform']['created_at'], $data['platform']['updated_at']);
            }

            $camera = DB::table('spec_cameras')->where('phone_id', $phone->id)->first();
            if ($camera) {
                $data['camera'] = (array) $camera;
                unset($data['camera']['id'], $data['camera']['phone_id'], $data['camera']['created_at'], $data['camera']['updated_at']);
            }

            $connectivity = DB::table('spec_connectivities')->where('phone_id', $phone->id)->first();
            if ($connectivity) {
                $data['connectivity'] = (array) $connectivity;
                unset($data['connectivity']['id'], $data['connectivity']['phone_id'], $data['connectivity']['created_at'], $data['connectivity']['updated_at']);
            }

            $battery = DB::table('spec_batteries')->where('phone_id', $phone->id)->first();
            if ($battery) {
                $data['battery'] = (array) $battery;
                unset($data['battery']['id'], $data['battery']['phone_id'], $data['battery']['created_at'], $data['battery']['updated_at']);
            }

            $benchmarks = DB::table('benchmarks')->where('phone_id', $phone->id)->first();
            if ($benchmarks) {
                $data['benchmarks'] = (array) $benchmarks;
                unset($data['benchmarks']['id'], $data['benchmarks']['phone_id'], $data['benchmarks']['created_at'], $data['benchmarks']['updated_at']);
            }

            if (! empty($data)) {
                $client->setDocument('phones', (string) $phone->id, $data, true);
                $merged++;
            } else {
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Merged specs into {$merged} phones, skipped {$skipped}.");

        return Command::SUCCESS;
    }
}
