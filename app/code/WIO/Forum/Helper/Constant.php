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

namespace WIO\Forum\Helper;

class Constant{
  
  const WIO_FORUM_ADMIN_ID = 1000000;
  const WIO_FORUM_ADMIN_USERNAME = 'admin';
  
  const WIO_FORUM_PER_PAGE = 'limit';
  const WIO_FORUM_PAGE_NUM = 'p';
  const WIO_FORUM_SORTING  = 'sort';
  
  const WIO_FORUM_USER_ID_PARAM = 'uid';
  
  /** SEARCH FORUM ***/
  const WIO_FORUM_SEARCH_PARAMNAME   = 'forum_search';
  const WIO_FORUM_SEARCH_REGISTRATED = 'forum_search_register';
  const WIO_FORUM_SEARCH_TYPE   = 'forum_search_type';
  const WIO_FORUM_SEARCH_TYPE_POST   = 'forum_search_post';
  const WIO_FORUM_SEARCH_TYPE_TOPIC  = 'forum_search_topic';
  const WIO_FORUM_SEARCH_TYPE_TOPIC_SORT  = 'forum_search_topic_sort';
  const WIO_FORUM_SEARCH_TYPE_TOPIC_PAGE  = 'forum_search_topic_page';
  const WIO_FORUM_SEARCH_TYPE_TOPIC_LIMIT = 'forum_search_topic_limit';
  const WIO_FORUM_SEARCH_TYPE_POST_SORT  = 'forum_search_post_sort';
  const WIO_FORUM_SEARCH_TYPE_POST_PAGE  = 'forum_search_post_page';
  const WIO_FORUM_SEARCH_TYPE_POST_LIMIT = 'forum_search_post_limit';
  
  /******* BOOKMARKS *******/
  const WIO_FORUM_TOPIC_IDS = 'topic_ids';
  const WIO_FORUM_BOOKMAR_REGISTRATED = 'forum_bookmark_topic_ids';
  
  /******* RSS ************/
  const WIO_FORUM_REGISTRAED_FORUMS = 'forum_rss_registrated_forums';
  const WIO_FORUM_REGISTRATED_TOPIC = 'forum_rss_registrated_topic';
  
  const WIO_FORUM_CREATED_TIME_SORT_FIELD = 'created_time';
  const WIO_FORUM_TIMESTAMP_TOPIC_POST = 'tmpst_post';
  const DEFAULT_SORT = 'desc';
  const WIO_FORUM_ID_PARAM_NAME = 'id';
  const WIO_FORUM_TOPIC_ID_PARAM_NAME = 'topic_id';
  const WIO_FORUM_POST_ID_PARAM_NAME  = 'post_id';
  const WIO_FORUM_PARENT_FORUM  = 'parent_forum';
  const WIO_FORUM_PARENT_TOPIC  = 'parent_topic';
  
  /* forum pager */
  const WIO_FORUM_SORT_KEY_REGISTER = 'forum_sort';
  const WIO_FORUM_PAGE_KEY_REGISTER = 'forum_page';
  const WIO_FORUM_LIMIT_KEY_REGISTER = 'forum_limit';
  const WIO_FORUM_DEFAULT_FORUM_PAGE_SIZE = 5;
  
  /* topic pager */
  const WIO_FORUM_TOPIC_SORT_KEY_REGISTER = 'topic_sort';
  const WIO_FORUM_TOPIC_PAGE_KEY_REGISTER = 'topic_page';
  const WIO_FORUM_TOPIC_LIMIT_KEY_REGISTER = 'topic_limit';
  const WIO_FORUM_DEFAULT_TOPIC_PAGE_SIZE = 10;
  
  /* posts pager */
  const WIO_FORUM_DEFAULT_POST_PAGE_SIZE  = 10; //10 should be
  const WIO_FORUM_POST_PAGE_KEY_REGISTER  = 'post_page';
  const WIO_FORUM_POST_LIMIT_KEY_REGISTER = 'post_limit';
  const WIO_FORUM_POST_SORT_KEY_REGISTER  = 'post_sort';
  
  const WIO_FORUM_CUSTOMER_MODEL_SESSION = 'wio_customer_model';
  
  /* edit registrated objects */
  const WIO_FORUM_EDIT_FORUM_OBJECT = 'edit_forum';
  const WIO_FORUM_EDIT_TOPIC_OBJECT = 'edit_topic';
  const WIO_FORUM_EDIT_POST_OBJECT  = 'edit_post';
  
  
  const WIO_FORUM_FRONTEND_ROUTE_NAME = 'wio_forum'; //do not edit this!!!
  
  const WIO_FORUM_AVATR_FILE_PATH  = 'wioforum/avatars';
  const WIO_FORUM_AVATAR_NO_IMAGE  = 'no_image.jpg';
  
  const WIO_FORUM_POST_BLOCK_ID = 'forum-post-reply-';
}
