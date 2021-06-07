<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UsersMigration extends Migration
{
    public function up()
    {
        //create table users
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'password' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'timestamp',
            ],
            'updated_at' => [
                'type' => 'timestamp',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        //drop table users if exists
        $this->forge->dropTable('users');
    }
}
