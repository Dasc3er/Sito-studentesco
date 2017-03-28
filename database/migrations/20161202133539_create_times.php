<?php

use Phinx\Migration\AbstractMigration;

class CreateTimes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('times');
        $table->addColumn('name', 'string')
            ->addTimestamps(null, null)
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->create();
    }
}
