# baiduwp-php
PanDownload网页复刻版，PHP语言版
本项目是依据[baiduwp](https://github.com/TkzcM/baiduwp)的javascript版本改写而来，仅供大家学习参考
另外使用的时候能不能保留一下作者信息呀（就是菜单栏的Made by Yuan_Tuo），谢~

## Demo
[已加密，暂不开放！](https://imwcr.cn/api/bdwp/)

## Setting
请在 `config.php` 中找：
```
define("BDUSS", "");
define("STOKEN", "");
define('IsCheckPassword', true);
$setpassword='请在这里填写密码啦ヾ(≧▽≦*)o';
```
- 前两项替换成你自己的SVIP信息就行，获取cookie方法见视频[BV1Yh411d7Gd](https://www.bilibili.com/video/BV1Yh411d7Gd)
- 第三项是是否需要密码的选项，
- 第四项是首页需要输入的密码，但是如果第三项为 false 则无效，
- 详细信息可见 `config.php` 的注释。

## Thanks
- [baiduwp](https://github.com/TkzcM/baiduwp): javascript 版本
- [PanDownload](https://pandownload.com): static pages
- [KinhDown](https://t.me/kinhdown): client type
- [PNL](https://www.lanzous.com/u/pnl): download method

## New Changes
- 以下修改由 [LC](https://github.com/lc6464 "LC") 完成
  - 新增自行选择是否需要密码功能
  - 配置与程序分离
  - 修复 errno 不是 -21 且不正常时 HTTP 500 服务器错误的问题