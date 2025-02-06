<?php
return [
    'left-menu-admin'   => [
        [
            'name'      => 'Huấn luyện viên',
            'route'     => 'admin.trainer.list',
            'icon'      => '<i class="fa-solid fa-user-tie"></i>',
            'role'      => [
                'admin', 
                'sub-admin',
            ],
        ],
        [
            'name'      => 'Quản lí trang',
            'route'     => 'admin.page.list',
            'icon'      => '<i class="fa-regular fa-file-lines"></i>',
            'role'      => [
                'admin',
            ],
        ],
        [
            'name'      => 'Quản lí Blog',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-blog"></i>',
            'role'      => [
                'admin', 
            ],
            'child'     => [
                [
                    'name'  => '1. Chuyên mục',
                    'route' => 'admin.categoryBlog.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. Tin tức',
                    'route' => 'admin.blog.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                
            ]
        ],
        [
            'name'      => 'Quản lí ảnh',
            'route'     => 'admin.image.list',
            'icon'      => '<i class="fa-regular fa-images"></i>',
            'role'      => [
                'admin', 
            ],
        ],
        [
            'name'      => 'Công cụ SEO',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-screwdriver-wrench"></i>',
            'role'      => [
                'admin', 
            ],
            'child'     => [
                [
                    'name'  => '1. Redirect 301',
                    'route' => 'admin.redirect.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                // [
                //     'name'  => '2. Auto tạo trang',
                //     'route' => 'admin.translate.viewCreateJobTranslateAndCreatePage',
                //     'icon'  => '<i data-feather=\'circle\'></i>'
                // ],
                // [
                //     'name'  => '3. Auto dịch',
                //     'route' => 'admin.translate.viewcreateJobTranslateContent',
                //     'icon'  => '<i data-feather=\'circle\'></i>'
                // ],
            ]
        ],
        [
            'name'      => 'Công nghệ AI',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-robot"></i>',
            'role'      => [
                'admin', 
            ],
            'child'     => [
                [
                    'name'  => '1. Prompt',
                    'route' => 'admin.prompt.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. API AI',
                    'route' => 'admin.apiai.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
            ]
        ],
        // [
        //     'name'      => 'Báo cáo',
        //     'route'     => '',
        //     'icon'      => '<i class="fa-solid fa-flag-checkered"></i>',
        //     'child'     => [
        //         [
        //             'name'  => '1. Auto dịch',
        //             'route' => 'admin.translate.list',
        //             'icon'  => '<i data-feather=\'circle\'></i>'
        //         ],
        //     ]
        // ],
    ]
];