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
        $genres = [
            'Pop Music',
            'Rock and Roll',
            'doo-wop',
            'Classic Rock',
        ];

        $faker = Faker\Factory::create();

        $contents = json_decode(file_get_contents(__DIR__ . '/records.json'), true);
        $recordsSeedData = [];
        foreach ($contents as $key => $record) {
            $randomTimeStamp= rand(-602821612,659482388);
            $recordsSeedData[] = [
                'name' => $record['name'],
                'genre' => array_rand($genres),
                'description' => $faker->realText(200),
                'published_at' => date("Y-m-d H:i:s", $randomTimeStamp),
                'cover_photo' => $this->createPhotoString($record['name']),
                'artist_id' => $record['artist_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
        }

        $this->query('TRUNCATE records RESTART IDENTITY CASCADE;');
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
