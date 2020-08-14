# baiduwp-php
PanDownload 网页复刻版，PHP 语言版<br/>
本项目是依据[baiduwp](https://github.com/TkzcM/baiduwp)的javascript版本改写而来，仅供大家学习参考<br/>
另外使用的时候能不能保留一下作者信息呀（就是菜单栏的Made by Yuan_Tuo），谢~

## Demo
[已加密，暂不开放！](https://imwcr.cn/api/bdwp/)

## Setting
请在 `config.php` 中找：
```
define('BDUSS', '');
define('STOKEN', '');
define('IsCheckPassword', true);
define('Password', '请在这里填写密码啦ヾ(≧▽≦*)o');
```
- 前两项替换成你自己的 SVIP 信息就行，获取 cookie 方法见视频 [BV1Yh411d7Gd](https://www.bilibili.com/video/BV1Yh411d7Gd)
- 第三项是是否需要密码的选项
- 第四项是首页需要输入的密码，但是如果第三项为 `false` 则无效
- 详细信息可见 `config.php` 的注释

## Thanks
- [baiduwp](https://github.com/TkzcM/baiduwp): javascript 版本
- [PanDownload](https://pandownload.com): static pages
- [KinhDown](https://t.me/kinhdown): client type
- [PNL](https://www.lanzous.com/u/pnl): download method

## New Changes
- 以下修改由 [LC](https://github.com/lc6464 "LC") 完成
  - 新增自行选择是否需要密码功能：2020-8-13
  - 配置、函数与程序分离（`php`）：2020-8-13 ~ 2020-8-14
  - 样式、JavaScript 与页面分离（`前端`）：2020-8-14
  - 修复 `errno` 不是 -21 且不正常时 HTTP 500 服务器错误的问题：2020-8-13
  - 修复 GET 方法访问 `index.php?download` 出错的问题：2020-8-13
  - 修复 POST 方法访问 `index.php?download` 参数不齐全出错的问题：2020-8-14
  - 修复未配置或者配置了普通用户的 BDUSS 和 STOKEN 时无法获取下载链接显示空链接的问题：2020-8-14
  - 优化数据传输：2020-8-14
  - 优化用户体验：2020-8-14

## 坑或不确定
- `static/functions.js`
  - 40 行