<?php

use Phinx\Migration\AbstractMigration;

class CreateCourseTime extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('course_time');
        $table->addColumn('time_id', 'integer')
            ->addColumn('course_id', 'integer')
            ->addTimestamps(null, null)
            ->addForeignKey('time_id', 'times', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('course_id', 'courses', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
