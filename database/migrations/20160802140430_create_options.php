<?php

use Phinx\Migration\AbstractMigration;

class CreateOptions extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('options');
        $table->addColumn('name', 'string')
            ->addColumn('type', 'string', ['limit' => 1500])
            ->addColumn('editable', 'boolean')
            ->addColumn('section', 'string')
            ->addTimestamps(null, null)
            ->addIndex(['name'], ['unique' => true])
            ->create();
    }
}
