<?php

use yii\db\Migration;

/**
 * Handles the creation of table `catalog`.
 */
class m181126_062300_create_catalog_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%catalog}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(32)->notNull(),
            'name' => $this->string(64)->notNull(),
            'cover' => $this->string(512)->defaultValue(''),
            'path' => $this->string(128)->defaultValue('0'),
            'sort' => $this->double(3)->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%catalog}}');
    }
}
