<?php
/**
 * 后台菜单配置
 *    'home' => [
 *       'name' => '首页',                // 菜单名称
 *       'icon' => 'icon-home',          // 图标 (class)
 *       'index' => 'index/index',         // 链接
 *     ],
 */
return [
    'index' => [
        'name' => '首页',
        'icon' => 'icon-home',
        'index' => 'index/index',
    ],
    'store' => [
        'name' => '权限',
        'icon' => 'icon-guanliyuan',
        'index' => 'store.user/index',
        'submenu' => [
            [
                'name' => '管理员列表',
                'index' => 'store.user/index',
                'uris' => [
                    'store.user/index',
                    'store.user/add',
                    'store.user/edit',
                    'store.user/delete',
                ],
            ],
            [
                'name' => '角色管理',
                'index' => 'store.role/index',
                'uris' => [
                    'store.role/index',
                    'store.role/add',
                    'store.role/edit',
                    'store.role/delete',
                ],
            ],
        ]
    ],
    'user' => [
        'name' => '用户',
        'icon' => 'icon-user',
        'index' => 'user/index',
        'submenu' => [
            [
                'name' => '用户列表',
                'index' => 'user/index',
            ],
        ]
    ],
    'content' => [
        'name' => '内容',
        'icon' => 'icon-wenzhang',
        'index' => 'content.article/index',
        'submenu' => [
            [
                'name' => '文章管理',
                'active' => true,
                'submenu' => [
                    [
                        'name' => '文章列表',
                        'index' => 'content.article/index',
                        'uris' => [
                            'content.article/index',
                            'content.article/add',
                            'content.article/edit',
                        ]
                    ],
                    [
                        'name' => '文章分类',
                        'index' => 'content.article.category/index',
                        'uris' => [
                            'content.article.category/index',
                            'content.article.category/add',
                            'content.article.category/edit',
                        ]
                    ],
                ]
            ],
            [
                'name' => '文件库管理',
                'submenu' => [
                    [
                        'name' => '文件分组',
                        'index' => 'content.files.group/index',
                        'uris' => [
                            'content.files.group/index',
                            'content.files.group/add',
                            'content.files.group/edit',
                        ]
                    ],
                    [
                        'name' => '文件列表',
                        'index' => 'content.files/index'
                    ],
                    [
                        'name' => '回收站',
                        'index' => 'content.files/recycle',
                    ],
                ]
            ],
        ]
    ],
    'sms' => [
        'name' => '短信',
        'icon' => 'icon-wxapp',
        'color' => '#36b313',
        'index' => 'sms.template/index',
        'submenu' => [
            [
                'name' => '短信发送日志',
                'index' => 'sms.letterlog/index',
            ],
            [
                'name' => '短信模板',
                'index' => 'sms.template/index',
            ],

        ]
    ],
    'wxapp' => [
        'name' => '平台',
        'icon' => 'icon-wxapp',
        'color' => '#36b313',
        'index' => 'wxapp/setting',
        'submenu' => [
            [
                'name' => '小程序设置',
                'index' => 'wxapp/setting',
            ],
            [
                'name' => '帮助中心',
                'index' => 'wxapp.help/index',
                'uris' => [
                    'wxapp.help/index',
                    'wxapp.help/add',
                    'wxapp.help/edit'
                ]
            ],
        ],
    ],
    'apps' => [
        'name' => '应用',
        'icon' => 'icon-application',
        'is_svg' => true,   // 多色图标
        'index' => 'apps.test/index',
        'submenu' => [
            [
                'name' => '测试应用',
                'submenu' => [
                    [
                        'name' => '清理缓存',
                        'index' => 'setting.cache/clear'
                    ]
                ]
            ],
            [
                'name' => '系统设置',
                'index' => 'setting/store',
            ],
            [
                'name' => '上传设置',
                'index' => 'setting/storage',
            ],
        ],
    ],
    'setting' => [
        'name' => '设置',
        'icon' => 'icon-setting',
        'index' => 'setting/store',
        'submenu' => [
            [
                'name' => '系统设置',
                'index' => 'setting/store',
            ],
            [
                'name' => '上传设置',
                'index' => 'setting/storage',
            ],
            [
                'name' => '其他',
                'submenu' => [
                    [
                        'name' => '清理缓存',
                        'index' => 'setting.cache/clear'
                    ]
                ]
            ]
        ],
    ],
];
