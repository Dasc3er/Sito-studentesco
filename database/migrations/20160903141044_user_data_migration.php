<?php

use Phinx\Migration\AbstractMigration;

class UserDataMigration extends AbstractMigration
{
    protected $data = [
        [
            'name' => 'Admin',
            'username' => 'admin',
            'password' => 'admin',
            'email' => 'admin@gmail.com',
            'role' => 1,
            'state' => 1,
        ],
        [
            'name' => 'User',
            'username' => 'user',
            'password' => 'user',
            'email' => 'user@gmail.com',
            'role' => 0,
            'state' => 1,
        ],
    ];

    public function up()
    {
        foreach ($this->data as $key => $value) {
            $this->data[$key]['password'] = \Crypt::hashpassword($value['password']);
        }

        $table = $this->table('users');
        $table->insert($this->data)->save();
    }

    public function down()
    {
        foreach ($this->data as $value) {
            $this->execute('DELETE FROM users WHERE name="'.$value['name'].'"');
        }
    }
}
