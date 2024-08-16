<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSubscribersTable extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('subscribers')) {
            $table = $this->table('subscribers');
            $table->addColumn('email', 'string', ['limit' => 255])
                ->addColumn('email_verified', 'boolean', ['default' => false])
                ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->addColumn('updated_at', 'timestamp', [
                    'default' => 'CURRENT_TIMESTAMP',
                    'update' => 'CURRENT_TIMESTAMP'
                ])
                ->create();
        }
    }

    public function down(): void
    {
        if ($this->hasTable('subscribers')) {
            $this->table('subscribers')->drop()->save();
        }
    }
}
