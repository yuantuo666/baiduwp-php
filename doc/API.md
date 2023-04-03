# baiduwp-php

> v3.0.0

Base URLs:

# api.php 接口文档

## GET 获取解析统计

GET /api.php

### 请求参数

|名称|位置|类型|必选|说明|
|---|---|---|---|---|
|m|query|string| 否 |none|

> 返回示例

> 200 Response

```json
{
  "error": -1,
  "msg": "string"
}
```

### 返回结果

|状态码|状态码含义|说明|数据模型|
|---|---|---|---|
|200|[OK](https://tools.ietf.org/html/rfc7231#section-6.3.1)|成功|Inline|

### 返回数据结构

状态码 **200**

|名称|类型|必选|约束|中文名|说明|
|---|---|---|---|---|---|
|» error|integer|true|none|错误码|none|
|» msg|string|true|none|返回信息|HTML格式|

#### 枚举值

|属性|值|
|---|---|
|error|-1|
|error|0|

## GET 获取密码状态

GET /api.php

检查是否启用密码设置，是否完成密码验证

### 请求参数

|名称|位置|类型|必选|说明|
|---|---|---|---|---|
|PHPSESSID|cookie|string| 否 |需要带上PHPSESSID来判断密码是否正确，获取方法：请求一次，保存返回的PHPSESSID|
|m|query|string| 否 |none|

> 返回示例

> 200 Response

```json
{
  "status": 0,
  "msg": "string"
}
```

### 返回结果

|状态码|状态码含义|说明|数据模型|
|---|---|---|---|
|200|[OK](https://tools.ietf.org/html/rfc7231#section-6.3.1)|成功|Inline|

### 返回数据结构

状态码 **200**

|名称|类型|必选|约束|中文名|说明|
|---|---|---|---|---|---|
|» status|integer|true|none||密码状态|
|» msg|string|true|none||none|

#### 枚举值

|属性|值|
|---|---|
|status|0|
|status|1|
|status|2|

## GET 检查更新

GET /api.php

### 请求参数

|名称|位置|类型|必选|说明|
|---|---|---|---|---|
|m|query|string| 否 |none|

> 返回示例

> 200 Response

```json
{
  "code": 0,
  "version": "string",
  "PreRelease": true,
  "file_url": "string",
  "page_url": "string",
  "info": [
    "string"
  ],
  "now_version": "string",
  "have_update": true
}
```

### 返回结果

|状态码|状态码含义|说明|数据模型|
|---|---|---|---|
|200|[OK](https://tools.ietf.org/html/rfc7231#section-6.3.1)|成功|Inline|

### 返回数据结构

状态码 **200**

|名称|类型|必选|约束|中文名|说明|
|---|---|---|---|---|---|
|» code|integer|true|none|状态码|none|
|» version|string|true|none|最新版本|none|
|» PreRelease|boolean|true|none|是否为预发布|none|
|» file_url|string|true|none|文件地址|有附件时不为空|
|» page_url|string|true|none|下载页面|none|
|» info|[string]|true|none|相关信息|none|
|» now_version|string|true|none|目前版本|none|
|» have_update|boolean|true|none|是否有更新|none|

## POST 解析链接根目录

POST /api.php

> Body 请求参数

```yaml
surl: 1otNXu2-z1cp1s_f8Gwp17w
pwd: aaaa
password: "1"

```

### 请求参数

|名称|位置|类型|必选|说明|
|---|---|---|---|---|
|m|query|string| 否 |none|
|body|body|object| 否 |none|
|» surl|body|string| 是 |百度网盘分享链接短链接，去除https://pan.baidu.com/s/得到|
|» pwd|body|string| 否 |分享链接提取码|
|» password|body|string| 否 |站点密码，Cookie携带验证过的PHPSESSID可不设置|

> 返回示例

> 200 Response

```json
{
  "error": 0,
  "isroot": true,
  "dirdata": {
    "src": [
      "string"
    ],
    "timestamp": "string",
    "sign": "string",
    "randsk": "string",
    "shareid": "string",
    "surl": "string",
    "pwd": "string",
    "uk": "string"
  },
  "filenum": 0,
  "filedata": [
    {
      "isdir": 0,
      "name": "string",
      "fs_id": "string",
      "path": "string",
      "size": 0,
      "uploadtime": 0,
      "dlink": "string"
    }
  ]
}
```

### 返回结果

|状态码|状态码含义|说明|数据模型|
|---|---|---|---|
|200|[OK](https://tools.ietf.org/html/rfc7231#section-6.3.1)|成功|Inline|

### 返回数据结构

状态码 **200**

|名称|类型|必选|约束|中文名|说明|
|---|---|---|---|---|---|
|» error|integer|true|none|错误码|none|
|» isroot|boolean|true|none|是否为根目录|none|
|» dirdata|object|true|none|文件夹信息|none|
|»» src|[string]|true|none|当前文件夹组成src|none|
|»» timestamp|string|true|none|签名时间戳|获取下载地址需使用，和sign匹配|
|»» sign|string|true|none|签名|获取下载地址需使用，5分钟有效期|
|»» randsk|string|true|none||提取码鉴权参数，有提取码时一定需要|
|»» shareid|string|true|none||分享链接id|
|»» surl|string|true|none||分享链接短链接|
|»» pwd|string|true|none||分享链接提取码|
|»» uk|string|true|none||分享者id|
|» filenum|integer|true|none||文件/文件夹数量|
|» filedata|[object]|true|none||文件信息|
|»» isdir|integer|true|none||是否为目录|
|»» name|string|true|none||文件名|
|»» fs_id|string|false|none||仅文件有，文件唯一id|
|»» path|string|false|none||仅文件夹有，绝对路径|
|»» size|integer|true|none||大小|
|»» uploadtime|integer|true|none||创建时间|
|»» dlink|string|false|none||仅文件有，dlink下载地址|

## GET 获取上次解析数据

GET /api.php

### 请求参数

|名称|位置|类型|必选|说明|
|---|---|---|---|---|
|m|query|string| 否 |none|

> 返回示例

> 200 Response

```json
{
  "error": -1,
  "msg": "string",
  "svipstate": 0,
  "sviptips": "string"
}
```

### 返回结果

|状态码|状态码含义|说明|数据模型|
|---|---|---|---|
|200|[OK](https://tools.ietf.org/html/rfc7231#section-6.3.1)|成功|Inline|

### 返回数据结构

状态码 **200**

|名称|类型|必选|约束|中文名|说明|
|---|---|---|---|---|---|
|» error|integer|true|none|错误码|none|
|» msg|string|true|none|上次解析数据|HTML格式返回|
|» svipstate|integer|true|none|会员状态|none|
|» sviptips|string|true|none|状态信息|none|

#### 枚举值

|属性|值|
|---|---|
|error|-1|
|svipstate|0|
|svipstate|1|

## POST 解析链接文件夹

POST /api.php

> Body 请求参数

```yaml
surl: 1otNXu2-z1cp1s_f8Gwp17w
pwd: aaaa
password: "1"
dir: /测试文件
timestamp: "1680530665"
sign: f22114a3fea4cfeb8bc768a85bf2f4f6483ea505
randsk: p7NjPNOpKqcgcmz1SXU0MbvIOZR4vsR8D2lCS5Tp%2ByQ%3D

```

### 请求参数

|名称|位置|类型|必选|说明|
|---|---|---|---|---|
|m|query|string| 否 |none|
|body|body|object| 否 |none|
|» surl|body|string| 是 |百度网盘分享链接短链接，去除https://pan.baidu.com/s/得到|
|» pwd|body|string| 是 |分享链接提取码|
|» password|body|string| 否 |站点密码，Cookie携带验证过的PHPSESSID可不设置|
|» dir|body|string| 是 |none|
|» timestamp|body|string| 否 |none|
|» sign|body|string| 否 |none|
|» randsk|body|string| 否 |none|

> 返回示例

> 200 Response

```json
{
  "error": 0,
  "isroot": true,
  "dirdata": {
    "src": [
      {}
    ],
    "timestamp": "string",
    "sign": "string",
    "randsk": "string",
    "shareid": "string",
    "surl": "string",
    "pwd": "string",
    "uk": "string"
  },
  "filenum": 0,
  "filedata": [
    {
      "isdir": 0,
      "name": "string",
      "fs_id": "string",
      "path": "string",
      "size": 0,
      "uploadtime": 0,
      "dlink": "string"
    }
  ]
}
```

### 返回结果

|状态码|状态码含义|说明|数据模型|
|---|---|---|---|
|200|[OK](https://tools.ietf.org/html/rfc7231#section-6.3.1)|成功|Inline|

### 返回数据结构

状态码 **200**

|名称|类型|必选|约束|中文名|说明|
|---|---|---|---|---|---|
|» error|integer|true|none|错误码|none|
|» isroot|boolean|true|none|是否为根目录|none|
|» dirdata|object|true|none|文件夹信息|none|
|»» src|[object]|true|none|当前文件夹组成src|none|
|»» timestamp|string|true|none|签名时间戳|获取下载地址需使用，和sign匹配|
|»» sign|string|true|none|签名|获取下载地址需使用，5分钟有效期|
|»» randsk|string|true|none||提取码鉴权参数，有提取码时一定需要|
|»» shareid|string|true|none||分享链接id|
|»» surl|string|true|none||分享链接短链接|
|»» pwd|string|true|none||分享链接提取码|
|»» uk|string|true|none||分享者id|
|» filenum|integer|true|none||文件/文件夹数量|
|» filedata|[object]|true|none||文件信息|
|»» isdir|integer|true|none||是否为目录|
|»» name|string|true|none||文件名|
|»» fs_id|string|false|none||仅文件有，文件唯一id|
|»» path|string|false|none||仅文件夹有，绝对路径|
|»» size|integer|true|none||大小|
|»» uploadtime|integer|true|none||创建时间|
|»» dlink|string|false|none||仅文件有，dlink下载地址|

## POST 获取下载地址

POST /api.php

> Body 请求参数

```yaml
surl: 1otNXu2-z1cp1s_f8Gwp17w
pwd: aaaa
fs_id: "577385254695324"
sign: f4b3af69a80ebd2280022ce553e1d61b5eda1fd7
timestamp: "1680536506"
randsk: p7NjPNOpKqcgcmz1SXU0MbvIOZR4vsR8D2lCS5Tp%2ByQ%3D
shareid: "3246295475"
uk: "1529664763"

```

### 请求参数

|名称|位置|类型|必选|说明|
|---|---|---|---|---|
|m|query|string| 否 |none|
|body|body|object| 否 |none|
|» surl|body|string| 否 |短分享链接，sign失效时重新获取使用|
|» pwd|body|string| 否 |分享链接提取码，sign失效时重新获取使用|
|» fs_id|body|string| 是 |文件唯一id|
|» sign|body|string| 是 |签名|
|» timestamp|body|string| 是 |签名时间戳|
|» randsk|body|string| 是 |提取码鉴权参数|
|» shareid|body|string| 是 |分享id|
|» uk|body|string| 是 |分享者id|

> 返回示例

> 200 Response

```json
{
  "error": 0,
  "msg": "string",
  "title": "string",
  "filedata": {
    "filename": "string",
    "size": "string",
    "path": "string",
    "uploadtime": 0,
    "md5": "string"
  },
  "directlink": "string",
  "user_agent": "string",
  "message": [
    "string"
  ]
}
```

### 返回结果

|状态码|状态码含义|说明|数据模型|
|---|---|---|---|
|200|[OK](https://tools.ietf.org/html/rfc7231#section-6.3.1)|成功|Inline|

### 返回数据结构

状态码 **200**

|名称|类型|必选|约束|中文名|说明|
|---|---|---|---|---|---|
|» error|integer|true|none|错误码|具体错误参考错误信息|
|» msg|string|false|none|错误信息|有错误时返回|
|» title|string|false|none|错误标题|有错误时也不一定返回|
|» filedata|object|true|none|文件信息|none|
|»» filename|string|true|none||none|
|»» size|string|true|none||none|
|»» path|string|true|none||none|
|»» uploadtime|integer|true|none||none|
|»» md5|string|true|none||none|
|» directlink|string|true|none|真实直链|大于50MB文件需设置UA下载|
|» user_agent|string|true|none|下载时需要使用的UA|none|
|» message|[string]|true|none|运行信息|none|



