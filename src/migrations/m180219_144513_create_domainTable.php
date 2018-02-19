<?php

use yii\db\Migration;

/**
 * Class m180219_144513_create_domainTable
 */
class m180219_144513_create_domainTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('zone', [
            'id' => $this->primaryKey(),
            'zoneName' => $this->string()->notNull(),
            'version' => $this->integer()->notNull()->defaultValue(0),
        ]);

        $this->createTable('domain', [
            'id' => $this->primaryKey(),
            'recordType' => $this->string()->notNull(),
            'domainName' => $this->string()->notNull(),
            'host' => $this->string()->notNull(),
            'updatedAt' => $this->dateTime(),
            'createdAt' => $this->dateTime(),
            'zone_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-domain-zone_id',
            'domain',
            'zone_id',
            'zone',
            'id',
            'CASCADE'
        );

        // FIXME テスト用のデータ そのうち消す
        $this->insert('zone', [
            'zoneName' => 'test',
        ]);
        $this->insert('domain', [
            'zoneName' => 'test',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180219_144513_create_domainTable cannot be reverted.\n";
        $this->dropTable('domain');
        $this->dropTable('zone');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180219_144513_create_domainTable cannot be reverted.\n";

        return false;
    }
    */
}
