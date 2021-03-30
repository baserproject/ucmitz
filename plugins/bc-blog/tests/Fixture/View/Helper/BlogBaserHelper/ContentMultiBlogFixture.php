<?php
// TODO : コード確認要
return;
/**
 * ContentFixture
 */
class ContentMultiBlogFixture extends BaserTestFixture
{

	/**
	 * Name of the object
	 *
	 * @var string
	 */
	public $name = 'Content';

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '1',
			'site_id' => '0',
			'name' => '',
			'plugin' => 'Core',
			'type' => 'ContentFolder',
			'url' => '/',
			'parent_id' => null,
			'lft' => '1',
			'rght' => '8',
			'title' => 'baserCMS inc. [デモ]',
			'status' => 1,
			'created' => '2016-07-29 18:02:53',
			'description' => '',
			'eyecatch' => '',
			'author_id' => '1',
			'created_date' => null,
			'modified_date' => null,
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'modified' => '2016-07-29 18:04:23',
			'entity_id' => '1',
			'alias_id' => null,
			'site_root' => 1,
			'deleted_date' => null,
			'deleted' => 0,
			'layout_template' => 'default',
			'main_site_content_id' => null,
			'level' => 0,
			'self_status' => 1,
			'self_publish_begin' => null,
			'self_publish_end' => null,
			'exclude_menu' => 0,
			'blank_link' => 0
		],
		[
			'id' => '2',
			'site_id' => '0',
			'name' => 'news',
			'plugin' => 'Blog',
			'type' => 'BlogContent',
			'url' => '/news/',
			'parent_id' => '1',
			'lft' => '2',
			'rght' => '3',
			'title' => '新着情報',
			'status' => 1,
			'created' => '2016-07-31 15:01:41',
			'description' => '',
			'eyecatch' => '',
			'author_id' => '1',
			'created_date' => '2016-07-31 15:01:41',
			'modified_date' => null,
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'modified' => '2016-07-31 15:02:16',
			'entity_id' => '1',
			'alias_id' => null,
			'site_root' => 0,
			'deleted_date' => null,
			'deleted' => 0,
			'layout_template' => '',
			'main_site_content_id' => null,
			'level' => 2,
			'self_status' => 1,
			'self_publish_begin' => null,
			'self_publish_end' => null,
			'exclude_menu' => 0,
			'blank_link' => 0
		],
		[
			'id' => '3',
			'site_id' => '0',
			'name' => 'topics',
			'plugin' => 'Blog',
			'type' => 'BlogContent',
			'url' => '/topics/',
			'parent_id' => '1',
			'lft' => '4',
			'rght' => '5',
			'title' => 'トピックス',
			'status' => 1,
			'created' => '2016-07-31 15:01:41',
			'description' => '',
			'eyecatch' => '',
			'author_id' => '1',
			'created_date' => '2016-07-31 15:01:41',
			'modified_date' => null,
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'modified' => '2016-07-31 15:02:16',
			'entity_id' => '2',
			'alias_id' => null,
			'site_root' => 0,
			'deleted_date' => null,
			'deleted' => 0,
			'layout_template' => '',
			'main_site_content_id' => null,
			'level' => 2,
			'self_status' => 1,
			'self_publish_begin' => null,
			'self_publish_end' => null,
			'exclude_menu' => 0,
			'blank_link' => 0
		],
		[
			'id' => '4',
			'site_id' => '3',
			'name' => 'event',
			'plugin' => 'Blog',
			'type' => 'BlogContent',
			'url' => '/event/',
			'parent_id' => '1',
			'lft' => '4',
			'rght' => '5',
			'title' => 'イベント',
			'status' => 1,
			'created' => '2016-07-31 15:01:41',
			'description' => '',
			'eyecatch' => '',
			'author_id' => '1',
			'created_date' => '2016-07-31 15:01:41',
			'modified_date' => null,
			'publish_begin' => null,
			'publish_end' => null,
			'exclude_search' => 0,
			'modified' => '2016-07-31 15:02:16',
			'entity_id' => '3',
			'alias_id' => null,
			'site_root' => 0,
			'deleted_date' => null,
			'deleted' => 0,
			'layout_template' => '',
			'main_site_content_id' => null,
			'level' => 2,
			'self_status' => 1,
			'self_publish_begin' => null,
			'self_publish_end' => null,
			'exclude_menu' => 0,
			'blank_link' => 0
		],
	];

}
