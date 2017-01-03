<?php

use Phinx\Migration\AbstractMigration;

class CreateCourseUser extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('course_user');
        $table->addColumn('course_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addTimestamps(null, null)
            ->addForeignKey('course_id', 'courses', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
