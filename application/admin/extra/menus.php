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
    'store' => [
        'name' => '平台',
        'icon' => 'icon-shangcheng',
        'index' => 'store/index',
        'submenu' => [
            [
                'name' => '平台列表',
                'index' => 'store/index',
                'uris' => [
                    'store/index',
                    'store/add',
                ]
            ],
            [
                'name' => '回收站',
                'index' => 'store/recycle'
            ],
            [
                'name' => '权限管理',
                'index' => 'store.access/index'
            ]
        ],
    ],
    'apps' => [
        'name' => '应用',
        'icon' => 'icon-application',
        'is_svg' => true,   // 多色图标
        'index' => 'apps.test/index'
    ],
    'setting' => [
        'name' => '系统',
        'icon' => 'icon-shezhi',
        'index' => 'setting.cache/clear',
        'submenu' => [
            [
                'name' => '清理缓存',
                'index' => 'setting.cache/clear'
            ],
            [
                'name' => '环境检测',
                'index' => 'setting.science/index'
            ],
        ],
    ],
];
