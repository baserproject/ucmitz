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
            'BaserCore' => [
                'title' => 'baserコア',
                'prefixes' => [
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
                            ],
                            'Contents' => [
                                'title' => 'コンテンツ管理',
                                'actions' => [
                                    'index' => [
                                        'title' => '一覧',
                                        'message' => 'Api/Contents/add, Api/Contents/edit に対しても設定を行いますか? [checkbox]',
                                        // 依存先
                                        // 'dependents' => [
                                        //     'Api' => [
                                        //         'Contents' => [
                                        //             'add',
                                        //         ],
                                        //     ],
                                        // ],
                                    ]
                                ]
                            ],
                        ]
                    ],
                    'Mypage' => [
                        'title' => 'マイページ',
                        'controllers' => [
                            'Mypages' => [
                                'title' => 'マイページ',
                                'actions' => [
                                    'profile_edit' => [
                                        'title' => 'プロフィール編集',
                                    ],
                                ],
                            ]
                        ],
                    ],
                    'Api' => [
                        'title' => 'Api',
                        'controllers' => [
                            'Contents' => [
                                'title' => 'コンテンツ管理',
                                'actions' => [
                                    'add' => [
                                        'title' => '追加',
                                    ],
                                    'edit' => [
                                        'title' => '編集',
                                    ],
                                ],
                            ]
                        ],
                    ]
                ]
            ],
            'BcBlog' => [
                'title' => 'ブログ',
                'prefixes' => [
                    'Api' => [
                        'title' => 'Api',
                        'controllers' => [
                            'BlogPosts' => [
                                'title' => 'ブログ記事',
                                'actions' => [
                                    'index' => [
                                        'title' => '一覧',
                                        'parameters' => [
                                            'published' => [
                                                'auth' => 'Admin',
                                            ],
                                        ],
                                    ],
                                ],
                            ]
                        ],
                    ],
                ],
            ]
        ]
    ]
];
