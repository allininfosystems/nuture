<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

namespace WIO\Forum\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

  public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
    $installer = $setup;

    $installer->startSetup();
    
    /* forum_moderator */
    $table_moderator = $installer->getConnection()
            ->newTable($installer->getTable('forum_moderator'))
            ->addColumn(
                    'moderator_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'autoincrement' => true], 'Id'
            )
            ->addColumn(
                    'system_user_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Magento Customer Id'
            )
            ->addColumn(
            'user_website_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Magento Website Id'
    );
    $installer->getConnection()->createTable($table_moderator);
    

    /* forum_access */
    $table_access = $installer->getConnection()
            ->newTable($installer->getTable('forum_access'))
            ->addColumn(
                    'entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'autoincrement' => true], 'Id'
            )
            ->addColumn(
                    'forum_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Forum Id'
            )
            ->addColumn(
            'group_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Magento Customer Group'
    );
    $installer->getConnection()->createTable($table_access);


    /* forum_moderator 
      $table = $installer->getConnection()
      ->newTable($installer->getTable('forum_moderator'))
      ->addColumn(
      'moderator_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'autoincrement' => true], 'Id'
      )
      ->addColumn(
      'system_user_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Magento User Id'
      )
      ->addColumn(
      'user_website_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Magento Webiste Id'
      );
      $installer->getConnection()->createTable($table);
     */


    $table_visitors = $installer->getConnection()
            ->newTable($installer->getTable('forum_visitor'))
            ->addColumn(
            'user_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'autoincrement' => true], 'Id'
            )
            ->addColumn(
            'system_user_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Magento User Id'
            )
            ->addColumn(
            'topic_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Module Forum Id'
            )
            ->addColumn(
            'session_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => true], 'User Session Id'
            )
            ->addColumn(
            'time_visited', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null], 'Time of visiting'
            )
            ->addColumn(
            'parent_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Module Forum Id'
            )
            ->addColumn(
            'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0], 'Magento Store Id'
            )
    ;
    $installer->getConnection()->createTable($table_visitors);

    /* forum_notification */
    $table_notification = $installer->getConnection()
            ->newTable($installer->getTable('forum_notification'))
            ->addColumn(
                    'notify_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'autoincrement' => true], 'Id'
            )
            ->addColumn(
                    'system_user_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Magento User Id'
            )
            ->addColumn(
                    'topic_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Module Forum Id'
            )
            ->addColumn(
            'hash', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => true], 'Access hash'
    );

    $installer->getConnection()->createTable($table_notification);

    /* forum_post */

    $table_posts = $installer->getConnection()
            ->newTable($installer->getTable('forum_post'))
            ->addColumn(
                    'post_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'autoincrement' => true], 'Id'
            )
            ->addColumn(
                    'parent_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Module Topic Id'
            )
            ->addColumn(
                    'forum_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Module Forum Id'
            )
            ->addColumn(
                    'system_user_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Magento User Id'
            )
            ->addColumn(
                    'post', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Post HTML'
            )
            ->addColumn(
                    'post_orig', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Post without HTML'
            )
            ->addColumn(
                    'created_time', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null], 'Time of creation'
            )
            ->addColumn(
                    'update_time', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null], 'Time of last update'
            )
            ->addColumn(
                    'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 1], 'Is post visible'
            )
            ->addColumn(
                    'product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Post assigned to product'
            )
            ->addColumn(
                    'is_sticky', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0], 'Always on top'
            )
            ->addColumn(
                    'is_deleted', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0], 'Removed post'
            )
            ->addColumn(
            'tmpst', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Linux timestamp of creation'
    );


    $installer->getConnection()->createTable($table_posts);


    /* forum_privatemessages */

    $table_private_messages = $installer->getConnection()
            ->newTable($installer->getTable('forum_privatemessages'))
            ->addColumn(
                    'pm_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'autoincrement' => true], 'Id'
            )
            ->addColumn(
                    'date_sent', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null], 'Time of creation'
            )
            ->addColumn(
                    'message', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Message Text'
            )
            ->addColumn(
                    'subject', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Message Subject'
            )
            ->addColumn(
                    'parent_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Parent Message Id'
            )
            ->addColumn(
                    'is_primary', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0], 'First Message'
            )
            ->addColumn(
                    'sent_from', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 12, ['default' => 0, 'nullable' => false], 'Magento User Id sender'
            )
            ->addColumn(
                    'sent_to', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 12, ['default' => 0, 'nullable' => false], 'Magento User Id reciever'
            )
            ->addColumn(
                    'is_trash', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0], 'Trash message'
            )
            ->addColumn(
                    'is_read', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0], 'Read message'
            )
            ->addColumn(
            'is_deleted', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0], 'Deleted message'
    );

    $installer->getConnection()->createTable($table_private_messages);

    /* forum_topic */

    $table_topics = $installer->getConnection()
            ->newTable($installer->getTable('forum_topic'))
            ->addColumn(
                    'topic_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'autoincrement' => true], 'Id'
            )
            ->addColumn(
                    'parent_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Parent Topic Id(if exists)'
            )
            ->addColumn(
                    'system_user_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Magento User Id'
            )
            ->addColumn(
                    'is_category', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0], 'Main Forum'
            )
            ->addColumn(
                    'created_time', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null], 'Time of creation'
            )
            ->addColumn(
                    'update_time', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null], 'Time of last update'
            )
            ->addColumn(
                    'title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Topic Title'
            )
            ->addColumn(
                    'description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Topic Description'
            )
            ->addColumn(
                    'url_text', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Url rewrite for topic'
            )
            ->addColumn(
                    'meta_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Meta Tag Description'
            )
            ->addColumn(
                    'meta_keywords', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Meta Tag Keywords'
            )
            ->addColumn(
                    'priority', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 3, ['default' => 0, 'nullable' => false], 'Topic Priority'
            )
            ->addColumn(
                    'icon_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => null, 'nullable' => true], 'Special Icon Id(text)'
            )
            ->addColumn(
                    'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 1], 'Is post visible'
            )
            ->addColumn(
                    'is_subtopic', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0], 'Is sub topic'
            )
            ->addColumn(
                    'is_deleted', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => false, 'default' => 0], 'Is deleted'
            )
            ->addColumn(
                    'product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0], 'Product Id'
            )
            ->addColumn(
                    'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0], 'Magento Store Id'
            )
            ->addColumn(
                    'total_views', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0], 'Total Topic Views(topics only)'
            )
            ->addColumn(
                    'tmpst', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Linux timestamp of creation'
            )
            ->addColumn(
                    'total_topics', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0], 'Total Topics in'
            )
            ->addColumn(
                    'total_posts', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0], 'Total Posts in'
            )
            ->addColumn(
                    'last_post_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0], 'Lats Post Id'
            )
            ->addColumn(
            'tmpst_post', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0], 'Linux Timstamp of last post in topic(forum)'
    );


    $installer->getConnection()->createTable($table_topics);

    /* forum_usersettings */

    $table_usersettings = $installer->getConnection()
            ->newTable($installer->getTable('forum_usersettings'))
            ->addColumn(
            'user_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true, 'autoincrement' => true], 'Id'
            )
            ->addColumn(
            'system_user_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Magento User Id'
            )
            ->addColumn(
            'nickname', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Custom user nickname'
            )
            ->addColumn(
            'signature', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Custom user signature'
            )
            ->addColumn(
            'avatar', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['default' => null, 'nullable' => true], 'Custom user avatar filename'
            )
            ->addColumn(
            'website_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['default' => 0, 'nullable' => false], 'Magento User Website Id'
    );

    $installer->getConnection()->createTable($table_usersettings);
    $installer->endSetup();
  }

}
