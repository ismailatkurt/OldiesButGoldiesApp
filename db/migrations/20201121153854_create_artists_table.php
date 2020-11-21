<?php

use Phinx\Db\Adapter\PostgresAdapter;
use Phinx\Migration\AbstractMigration;

final class CreateArtistsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('artists');
        $table->addColumn('name', PostgresAdapter::PHINX_TYPE_STRING, ['null' => false])
            ->addColumn('photo', PostgresAdapter::PHINX_TYPE_STRING, ['null' => true])
            ->addColumn('created_at', PostgresAdapter::PHINX_TYPE_DATETIME, ['null' => false])
            ->addColumn('updated_at', PostgresAdapter::PHINX_TYPE_DATETIME, ['null' => true])
            ->addIndex(['name'], [
                'unique' => true,
                'name' => 'idx_artists_name'
            ])
            ->create();
    }
}
