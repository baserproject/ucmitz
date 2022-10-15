<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

return [
    'BcApp' => [
       'permissions' => [
        'baser-core' => [
            'Admin' => [
                'title' => '管理画面',
                'controllers' => [
                    'Pages' => [
                        'title' => '固定ページ',
                        'actions' => [
                            'view' => [
                                'title' => '表示',
                                'default' => [
                                    'auth' => 'GET',
                                    'allow' => true
                                ],
                            ],
                            'edit' => [
                                'title' => '編集',
                                'default' => [
                                    'auth' => 'POST',
                                    'allow' => true
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
