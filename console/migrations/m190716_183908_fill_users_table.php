<?php

use yii\db\Migration;
use common\models\UserStatusActive;
use common\models\UserStatusInactive;

/**
 * Class m190716_183908_fill_users_table
 */
class m190716_183908_fill_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->batchInsert('user', ['name', 'status', 'email', 'createdAt', 'updatedAt'], [
            ['Name1', UserStatusActive::CODE, 'test1@test.ru', 1563302247, 1563302322],
            ['Name2', UserStatusInactive::CODE, 'test2@test.ru', 1563302322, 1563302500],
            ['Name3', UserStatusActive::CODE, 'test3@test.ru', 1563302247, 1563302322],
            ['Name4', UserStatusActive::CODE, 'test4@test.ru', 1563302322, 1563302500],
            ['Name5', UserStatusInactive::CODE, 'test5@test.ru', 1563302322, 1563302500],
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190716_183908_fill_users_table cannot be reverted.\n";

        return false;
    }
    */
}
