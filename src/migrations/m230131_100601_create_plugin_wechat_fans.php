<?php

use yii\db\Migration;

/**
 * Class m230131_100601_create_plugin_wechat_fans
 */
class m230131_100601_create_plugin_wechat_fans extends Migration
{
    private string $tableName = '{{%plugin_wechat_fans}}';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql' || $this->db->driverName === 'mariadb') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->execute('SET foreign_key_checks = 0');
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->comment('ID'),
            'merchant_id' => $this->integer()->comment('Merchant'),
            'member_id' => $this->integer()->null()->defaultValue(0)->comment('Member ID'),
            'unionid' => $this->string(64)->null()->comment('Union ID'),
            'openid' => $this->string(64)->notNull()->comment('OpenID'),
            'nickname' => $this->string(50)->null()->comment('Nickname'),
            'head_portrait' => $this->string(255)->null()->comment('head portrait'),
            'gender' => $this->tinyInteger(2)->defaultValue(0)
                ->comment('Gender[0:unknown,1:male,2:female]'),
            'subscribe' => $this->tinyInteger(1)->defaultValue(1)
                ->comment('Subscribe[0:unsubscribed,1:subscribed]'),
            'subscribe_time' => $this->integer()->null()->defaultValue(0)
                ->comment('Subscribe time'),
            'subscribe_scene' => $this->string(50)->null()->comment('Subscribe scene'),
            'unsubscribe_time' => $this->integer()->null()->defaultValue(0)
                ->comment('Unsubscribe time'),
            'group_id' => $this->integer()->defaultValue(0)->comment('Group ID'),
            'tagid_list' => $this->binary()->comment('Tag list'),
            'last_longitude' => $this->string(10)->null()->comment('Last longitude'),
            'last_latitude' => $this->string(10)->null()->comment('Last latitude'),
            'last_address' => $this->string(255)->null()->comment('Last address'),
            'last_updated' => $this->integer()->null()->defaultValue(0)->comment('Last update time'),
            'country' => $this->string(100)->null()->comment('Country'),
            'province' => $this->string(100)->null()->comment('Province'),
            'city' => $this->string(100)->null()->comment('City'),
            'remark' => $this->string(30)->null()->comment('Remark'),
            'status' => $this->tinyInteger(4)->defaultValue(1)
                ->comment('Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => $this->integer()->notNull()->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Created at'),
            'updated_at' => $this->integer()->notNull()
                ->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Updated at')
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Plugin wechat fans table');

        $this->createIndex('UNQ_MerchantOpenId', $this->tableName, ['openid', 'merchant_id'], true);
        $this->createIndex('IDX_WechatNickname', $this->tableName,'nickname');
        $this->createIndex('IDX_WechatMemberId', $this->tableName,'member_id');

        $this->execute('SET foreign_key_checks = 1');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropIndex('UNQ_MerchantOpenId', $this->tableName);
        $this->dropIndex('IDX_WechatNickname', $this->tableName);
        $this->dropIndex('IDX_WechatMemberId', $this->tableName);
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1');
    }
}
