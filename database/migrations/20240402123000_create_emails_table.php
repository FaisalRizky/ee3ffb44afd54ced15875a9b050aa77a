<?php

use Phinx\Migration\AbstractMigration;

class CreateEmailsTable extends AbstractMigration
{
    public function up()
    {
        // Check if the table already exists
        if ($this->hasTable('emails')) {
            echo "Table 'emails' already exists.\n";
        } else {
            // Create the table
            $table = $this->table('emails');
            $table->addColumn('module', 'string', ['limit' => 100])
                  ->addColumn('emailId', 'string', ['limit' => 100])
                  ->addColumn('recipient', 'string', ['limit' => 100])
                  ->addColumn('subject', 'string', ['limit' => 100])
                  ->addColumn('content', 'text')
                  ->addColumn('status', 'string', ['limit' => 20, 'default' => 'processing'])
                  ->addColumn('sent_at', 'timestamp', ['null' => true])
                  ->addColumn('remarks', 'string', ['limit' => 255, 'null' => true])
                  ->addTimestamps()
                  ->create();

            echo "Migration successful: Table 'emails' created.\n";
        }
    }

    public function down()
    {
        // Drop the table if it exists
        if ($this->hasTable('emails')) {
            $this->table('emails')->drop()->save();
            echo "Migration rollback successful: Table 'emails' dropped.\n";
        } else {
            echo "Table 'emails' does not exist, no rollback needed.\n";
        }
    }
}
