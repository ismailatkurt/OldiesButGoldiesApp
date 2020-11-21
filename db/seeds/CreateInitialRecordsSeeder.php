<?php

use Phinx\Seed\AbstractSeed;

class CreateInitialRecordsSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [
            'CreateInitialArtistsSeeder'
        ];
    }

    public function run(): void
    {
        $contents = json_decode(file_get_contents(__DIR__ . '/records.json'), true);
        $recordsSeedData = [];
        foreach ($contents as $key => $record) {
            $recordsSeedData[] = [
                'name' => $record['name'],
                'cover_photo' => $this->createPhotoString($record['name']),
                'artist_id' => $record['artist_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
        }

        $records = $this->table('records');
        $records->insert($recordsSeedData)
            ->saveData();
    }

    private function createPhotoString($name)
    {
        return strtolower(
                trim(
                    preg_replace('/[^A-Za-z0-9-]+/', '-', $name)
                )
            ) . '.jpg';
    }
}
