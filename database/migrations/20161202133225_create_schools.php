<?php

use Phinx\Migration\AbstractMigration;

class CreateSchools extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('schools');
        $table->addColumn('name', 'string')
            ->addTimestamps(null, null)
            ->create();
    }
}
