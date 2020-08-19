<?php 
// 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录 https://ram.console.aliyun.com 创建RAM账号。
/* 全快充*/
$config['accessKeyId'] = "LTAIAHfFbIjSMpQm";
$config['accessKeySecret'] = "oGadXodRbuBNH8ue8cwIsw3AEBdrXN";
/* 测试。线上不能使用！*/
/*$config['accessKeyId'] = "LTAIU0738wUViXkC";
$config['accessKeySecret'] = "7c0rBxtStMJEn4Ky2aEQQBohBe6Xpx";*/

/* 对象存储 */
$config['endpoint'] = "http://oss-cn-beijing.aliyuncs.com"; // Region请按实际情况填写 - 北京
// $config['bucket'] = "bucket-20190218"; // 存储空间名称:不同业务选择不一样bucket
/* 对象存储 - end */

/* 阿里物联网*/
/* 全快充*/
$config['regionId'] = 'cn-shanghai'; // 设备所在地域, 'cn-hangzhou'
/* 阿里物联网 - end*/