# baiduwp-php
PanDownload 网页复刻版，PHP 语言版<br/>
本项目是依据 [baiduwp](https://github.com/TkzcM/baiduwp "baiduwp") 的 JavaScript 版本改写而来，仅供大家学习参考<br/>
希望在使用时能够保留导航栏的 Made by Yuan_Tuo ，感谢！

因为作者开学了，所以项目将不再频繁更新。

一般情况下网页版不会出现问题，第一次使用就失败一般是设置的问题；如果使用一段时间后失效，一般是账号问题或服务器IP被baidu封了；如果是方法失效，这个项目将关闭。

- 处理下载限速一般方法
  - 尝试重新分享文件，部分文件可能出现奇怪的问题
  - 耐心等待baidu解封账号
  - 更换后台SVIP账号
  - 更换服务器IP


## 黑名单
- http://byu5.cn/baiduwp/

以上网站使用本项目源码，未与作者联系而删除作者信息。
已经通知站长，请修改网站后联系我删除黑名单。

## Demo
[已加密，暂不开放！](https://imwcr.cn/api/bdwp/)

## 安装注意事项
- 使用了 `SESSION`，注意 **PHP 访问系统文件（夹）权限**问题
- 仅支持 **PHP 7 和 7+**！

## 视频教程
B站的视频应该是被举报了，现在已经锁定了，历经十二天终究是昙花一现。

[原视频备份](https://v.youku.com/v_show/id_XNDc5MDExMzAyMA====.html)

[LC](https://github.com/lc6464 "LC")优化版使用方法 [BV1dt4y1D7Cf](https://b23.tv/pfUrnp)


## Setting
请在 `config.php` 中找：
```
define('BDUSS', '');
define('STOKEN', '');
define('IsCheckPassword', true);
define('Password', '请在这里填写密码啦！ヾ(≧▽≦*)o');
```
- 前两项填入你自己的 SVIP 信息就行，获取 cookie 方法见视频 [备份](https://v.youku.com/v_show/id_XNDc5MDExMzAyMA====.html)
- 其中123换成SVIP的BDUSS，456换成SVIP的STOKEN
- 第三项是是否需要密码的选项
- 第四项是首页需要输入的密码，但是如果第三项为 `false` 则无效
- 详细信息可见 `config.php` 的注释

例如，你的SVIP的BDUSS是 `123` ，STOKEN是 `456` ，开启密码并且设置为 `789` ，那么应该将 `config.php` 中设置成以下的代码：

```
define('BDUSS', '123');
define('STOKEN', '456');
define('IsCheckPassword', true);
define('Password', '789');
```

## Thanks
- [baiduwp JavaScript 版](https://github.com/TkzcM/baiduwp "GitHub 项目")
- [PanDownload 网站](https://pandownload.com/ "PanDownload 网站")
- [KinhDown 客户端](https://t.me/kinhdown/ "KinhDown 客户端")
- [PNL 下载方式](https://www.lanzous.com/u/pnl "PNL 下载方式")
- [LC优化版](https://github.com/lc6464 "LC")

## New Changes
- 当前版本：`1.4.1`
- 更新日期：2020-8-27
- 修改内容
  - 修改POST内容，让调用接口暂时失效
  - 增加直链解析，可以不设置UA下载（不过并不稳定，且只支持50MB以下文件）
