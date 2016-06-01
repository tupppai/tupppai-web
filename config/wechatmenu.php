<?php
    $main_url = 'http://'.env('MAIN_HOST');
    $app_download = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.psgod&g_f=991653#opened';

    $activity = [];
    $activity['graduation'] = $main_url."/services/index.html#activity/index";
//[{"type":"view","name":"\u7537\u795e\u6d3b\u52a8","url":"http:\/\/www.tupppai.com\/boys\/index\/index","sub_button":[]},{"type":"view","name":"App\u4e0b\u8f7d","url":"http:\/\/a.app.qq.com\/o\/simple.jsp?pkgname=com.psgod&g_f=991653#opened","sub_button":[]}]
    return [
        //服务号 图派tupppai
	    'wxa0b2dda705508552'=>[
            [
                "name" => "图派",
                "type" => "view",
                "url"  => $main_url.'/services/index.html',
            ],
            [
                "name"=> '活动',
                'sub_button' => [
                    [
                        "type" => "view",
                        "name" => "晒毕业照",
                        "url"  => $activity['graduation'],
                    ]
                ]
            ],
            [
                "name" => '下载APP',
                "type" => "view",
                "url"  => $app_download,
            ],
        ],

        //图派订阅号 图派社区
        'wx386ec5d1292a1e8f'=>[
            [
                "name"       => "小教程",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "勾搭大神",
                        "url"  => $main_url . '/services/index.html#personal/index/',
                    ],
                    [
                        "type" => "view",
                        "name" => '最新教程',
                        "url"  => $app_download,
                    ]
                ],
            ],
            [
                "name"       => "图派玩耍",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "调戏小哥",
                        "url"  => $main_url . '/services/index.html#personal/index/',
                    ],
                    [
                        "type" => "view",
                        "name" => '投稿教程',
                        "url"  => $app_download,
                    ],
                    [
                        "type" => "view",
                        "name" => '推荐活动',
                        "url"  => $app_download,
                    ]
                ],
            ],
            [
                "name"       => "更多",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "帮P",
                        "url"  => $main_url . '/services/index.html#personal/index/',
                    ],
                    [
                        "type" => "view",
                        "name" => "我的作品",
                        "url"  => $main_url . '/services/index.html#personal/index/',
                    ],
                    [
                        "type" => "view",
                        "name" => "进行中",
                        "url"  => $main_url . '/services/index.html#personal/index/',
                    ],
                    [
                        "type" => "view",
                        "name" => '下载APP',
                        "url"  => $app_download,
                    ]
                ],
            ],
        ],

    ];
