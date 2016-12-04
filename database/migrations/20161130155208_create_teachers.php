<?php

use Phinx\Migration\AbstractMigration;

class CreateTeachers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('teachers');
        $table->addColumn('name', 'string')
            ->addTimestamps(null, null)
            ->create();
    }
}
