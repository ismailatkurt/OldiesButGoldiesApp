<?php

use Phinx\Db\Adapter\PostgresAdapter;
use Phinx\Migration\AbstractMigration;

final class CreateRecordsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('records');
        $table->addColumn('name', PostgresAdapter::PHINX_TYPE_STRING, ['null' => false])
            ->addColumn('cover_photo', PostgresAdapter::PHINX_TYPE_STRING, ['null' => true])
            ->addColumn('artist_id', PostgresAdapter::PHINX_TYPE_INTEGER, ['null' => false])
            ->addForeignKey('artist_id', 'artists', 'id')
            ->addColumn('created_at', PostgresAdapter::PHINX_TYPE_DATETIME, ['null' => false])
            ->addColumn('updated_at', PostgresAdapter::PHINX_TYPE_DATETIME, ['null' => true])
            ->addIndex(['name'], [
                'unique' => true,
                'name' => 'idx_records_name'
            ])
            ->create();

    }
}
