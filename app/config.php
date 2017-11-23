<?php

return [
  'default_return_type'=>'json',
  // 扩展函数文件
  'extra_file_list'    => [ APP_PATH . 'helper.php', THINK_PATH . 'helper.php'],
  //响应配置
  //数据返回模版
  'return' => [
                  "code" => 200, //响应码
                  "success" => true,
                  "data" => [], //多条数据
                  "obj" => [] //单条数据
              ], 
  //响应码对应说明
  'codeMap' => [
                    "200"=>"响应成功",
                    "4000"=>"accessToken无效！",
                    "4001"=>"验证码发送失败",
                    "4002"=>"参数输入错误",
                    "4003"=>"数据库不匹配",
                    "4011"=>"数据库插入或者更改失败",
                    "4020"=>"请求的参数不全"
            ],
  //短信发送平台配置
  'smsConfig' => [
                  "Appkey" => "5f1db12aa6cc3f4dd0b8065a80d7e9a7",
                  "errorMap" => [
                        '-10' =>  '验证信息失败	检查api key是否和各种中心内的一致，调用传入是否正确',
                        '-11' =>  '用户接口被禁用	滥发违规内容，验证码被刷等，请联系客服解除',
                        '-20' =>  '短信余额不足	进入个人中心购买充值',
                        '-30' =>  '短信内容为空	检查调用传入参数：message',
                        '-31' =>  '短信内容存在敏感词	接口会同时返回  hit 属性提供敏感词说明，请修改短信内容，更换词语',
                        '-32' =>  '短信内容缺少签名信息	短信内容末尾增加签名信息eg.【公司名称】',
                        '-33' =>  '短信过长，超过300字（含签名）	调整短信内容或拆分为多条进行发送',
                        '-34' =>  '签名不可用	在后台 短信->签名管理下进行添加签名',
                        '-40' =>  '错误的手机号	检查手机号是否正确',
                        '-41' =>  '号码在黑名单中	号码因频繁发送或其他原因暂停发送，请联系客服确认',
                        '-42' =>  '验证码类短信发送频率过快	前台增加60秒获取限制',
                        '-50' =>  '请求发送IP不在白名单内	查看触发短信IP白名单的设置'
                     ]
                ],
  'cloudinary' => [
                  'cloud_name' => 'lsiten',
                  'api_key' => '384266963481284',
                  'api_secret' => 'p0qzfk-ibGWPydz7DGr5jzL_F3Q',
                  'base' => 'http://res.cloudinary.com/lsiten',
                  'apiBase' => 'https://api.cloudinary.com/v1_1/lsiten',
                  'image' => '/image/upload',
                  'video' => '/video/upload',
                  'audio' => '/raw/upload'
  ],
  'qiniu' => [
            "bucket"=>"dogapp",
            "AK"=>"-tVa9-F1avUf-usuodBBzjNiliLTTIwkmO9zjV5l",
            "SK"=>"jRLU5QK0rNg-IdkcNXx9hIrjVdAg5A1PMuFTYsRu"
  ]
];
