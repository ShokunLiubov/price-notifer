<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class EmailVerification extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('email_verification')) {
            $table = $this->table('email_verification');
            $table->addColumn('token', 'string', ['limit' => 255])
                ->addColumn('subscriber_id', 'integer', ['signed' => false])
                ->addForeignKey(
                    'subscriber_id',
                    'subscribers',
                    'id',
                    ['delete' => 'CASCADE', 'update' => 'NO_ACTION']
                )
                ->create();
        }
    }

    public function down(): void
    {
        if ($this->hasTable('email_verification')) {
            $table = $this->table('email_verification');
            $table->dropForeignKey('subscriber_id');

            $table->drop()->save();
        }
    }
}
