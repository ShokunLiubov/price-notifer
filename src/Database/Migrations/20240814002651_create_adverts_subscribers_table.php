<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAdvertsSubscribersTable extends AbstractMigration
{
    public function up(): void
    {
        if ($this->hasTable('adverts')
            && $this->hasTable('subscribers')
            && !$this->hasTable('advert_subscriber')) {
            $table = $this->table('advert_subscriber');
            $table->addColumn('advert_id', 'integer', ['signed' => false])
                ->addColumn('subscriber_id', 'integer', ['signed' => false])
                ->addForeignKey(
                    'advert_id',
                    'adverts',
                    'id',
                    ['delete' => 'CASCADE', 'update' => 'NO_ACTION']
                )
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
        if ($this->hasTable('advert_subscriber')) {
            $table = $this->table('advert_subscriber');
            $table->dropForeignKey('advert_id')
                ->dropForeignKey('subscription_id');

            $table->drop()->save();
        }
    }
}
