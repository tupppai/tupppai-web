<?php

class UserTest extends TestCase
{

    public $type = 'android';
    /**
     * @dataProvider userInfoProvider
     */
    public function testUserRegister(  $type, $code, $mobile, $password, $nickname, $avatar, $location, $sex, $openid, $auth, $avatar_url ){
        // $data = array(
        //     'type'       => 'mobile',
        //     'code'       => '123456',
        //     'mobile'     => "13800138000",
        //     'password'   => '123123',
        //     'nickname'   => 'bbb',
        //     'avatar'     => 'http://7u2spr.com1.z0.glb.clouddn.com/20150605-15425755715301a7625.jpg',
        //     'location'   =>  'aaa',
        //     'city'       =>  10,
        //     'province'   =>  32,
        //     'sex'        => '0',
        //     'openid'     => '',
        //     'auth'       =>  '',
        //     'avatar_url' =>  ''
        // );
        $data = array(
            'type'       => $type,
            'code'       => $code,
            'mobile'     => $mobile,
            'password'   => $password,
            'nickname'   => $nickname,
            'avatar'     => $avatar,
            'location'   => $location,
            'sex'        => $sex,
            'openid'     => $openid,
            'auth'       => $auth,
            'avatar_url' => $avatar_url
        );


        $res = json_decode( $this->post('/v1/user/save', $data) );

        $this->assertEquals( $res->data, '手机已注册');
        $this->assertEquals( $res->data, '请输入手机号码');
        $this->assertEquals( $res->data, '注册类型出错');
    }

    public function userInfoProvider(){
        $all = array(
            'types' => array(
                'correct' => array(
                    'mobile',
                    'weixin',
                    'weibo'
                ),
                'wrong' => array(
                    '',
                    NULL,
                    'aaaaaaaaaaa',
                    'a',
                    '汉字',
                    '123123',
                    '-1',
                    '23.34234',
                    'true'
                )
            ),
            'code' => array(
                'correct'=> array(
                    '123456',
                    123456,
                    '999999',
                    '000000'
                ),
                'wrong'=>array(
                    '-1',
                    NULL,
                    'abcdef',
                    '',
                    '-1',
                    '23.34234',
                    'true',
                    'false'
                )
            ),
            'mobile' => array(
                'correct' => array(
                    '13800138000',
                    '19000199000',
                    '17000000000',
                    '18000000000'
                ),
                'wrong' => array(
                    '-1',
                    '-1.2',
                    '3.6',
                    'true',
                    NULL,
                    '',
                    'adfasd',
                    '123',
                    '1231231231231231231'
                )
            ),
            'password' => array(
                'correct' => array(
                    '123456',
                    'asdfasdf',
                    '.,/;!@(*#)($@#)',
                    'jjhb234jj234',
                    'hk@KB!K',
                    'jk:L":!@2312',
                    '-1',
                    '23.123',
                    'ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss',
                    '23123.1231.1231'
                ),
                'wrong' => array(
                    '汉字密码汉字密码',
                    'a',
                    '123',
                    NULL,
                    '',
                    '1.2',
                    '-11.12',
                    '0'
                )
            ),
            'nickname' => array(
                'correct' => array(
                    'aaaa',
                    '中文汉字博大精深',
                    '123123',
                    '.....',
                    '1.02',
                    'true',
                    'false'
                ),
                'wrong' => array(
                    'admin',
                    'psgod',
                    'ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss'
                )
            ),
            'avatar' => array(
                'correct' => array(
                    'http://7u2spr.com1.z0.glb.clouddn.com/20150605-15425755715301a7625.jpg'
                ),
                'wrong' => array(
                    'http:///',
                    'asdfad',
                    '1123',
                    '0',
                    '-12',
                    'true',
                    'false'
                )
            ),
            'location' => array(
                'correct' => array(
                    ''
                ),
                'wrong' => array(
                    'true',
                    'false',
                    '0'
                )
            ),
            'sex' => array(
                'correct' => array(
                    '0',
                    '1'
                ),
                'wrong' => array(
                    'asd',
                    '',
                    '0',
                    '-53',
                    '223.023',
                    '323',
                    '...,12',
                    'true',
                    'false',
                    NULL
                )
            ),
            'openid' => array(
                'correct' => array(
                    '',
                    NULL
                ),
                'wrong' => array(
                    '123.021',
                    '-12312',
                    '32323',
                    'true',
                    'false',
                    'dasdf'
                )
            ),
            'auth' => array(
                'correct' => array(
                    '',
                    NULL
                ),
                'wrong' => array(
                    '123.021',
                    '-12312',
                    '32323',
                    'true',
                    'false',
                    'dasdf'
                )
            ),
            'avatar_url'=> array(
                'correct' => array(),
                'wrong' => array(
                    'http:///',
                    'sadfad',
                    '123.021',
                    '-12312',
                    '32323',
                    'true',
                    'false',
                    'dasdf'
                )
            )
        );

        $data = array();
        foreach( $all as $wrongKey => $wrongValues ){
            $d = array_keys( $all );
            $d = array_flip( $d );
            foreach( $all as $key => $values){
                if( $key == $wrongKey ){
                    continue;
                }
                $d[$key] = array_rand($values['correct']);
            }

            foreach ($wrongValues['wrong'] as $key => $value) {
                $d[$wrongKey] = $value;
                $data[] = $d;
            }
        }

        return $data;
    }

    public function testUserLogin(){
        $res = $this->get('/ask/index?size=15');

        if( empty($res->data) ) {
             $this->assertTrue(false);
        }

        $this->assertEquals( $res->data->uid, 1 );
        $this->assertEquals( $res->data->nickname, 'jq' );
        $this->assertEquals( $res->data->sex, 0 );
    }
}
