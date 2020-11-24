# duanship phpsdk

本项目提供了短视频去水印解析的服务

# 使用说明

首先安装这个包

```bash
composer require leavebn/duanship
```

然后使用：

```php
use Leavebn\Duanship\DuanAPI;

$dapi = new DuanAPI([
    "secretId"  => "填入你的secretId", 
    "secretKey" => "填入你的secretKey",
    "preParse"  => false // 是否需要返回更多信息，如视频尺寸和格式
]);

echo json_encode(
    $dapi->parse("https://h5.pipix.com/s/JajGA65/")
, JSON_UNESCAPED_UNICODE);

```

结果示例：

```json
{"code":200,"data":{"title":"视频标题","url":"http://xxx.mp4","from":"Douyin"}}
```

失败示例：

```json
{"code":109,"msg":"获取视频信息失败"}
```

