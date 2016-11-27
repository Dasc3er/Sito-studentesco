<?php

use Phinx\Migration\AbstractMigration;

class CreateVisits extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('visits');
        $table->addColumn('browser', 'string')
            ->addColumn('address', 'string')
            ->addTimestamps(null, null)
            ->create();
    }
}
