<?php

use Phinx\Seed\AbstractSeed;

class CreateInitialArtistsSeeder extends AbstractSeed
{
    public function run()
    {
        $contents = json_decode(file_get_contents( __DIR__ . '/artists.json'), true);
        $artistsSeedData = [];
        foreach ($contents as $artist) {
            $artistsSeedData[$artist['name']] = [
                'name' => $artist['name'],
                'photo' => $this->createPhotoString($artist['name']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
        }

        $artists = $this->table('artists');
        $artists->insert($artistsSeedData)
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
