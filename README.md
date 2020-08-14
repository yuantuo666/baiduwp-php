# baiduwp-php
PanDownload 网页复刻版，PHP 语言版<br/>
本项目是依据 [baiduwp](https://github.com/TkzcM/baiduwp) 的 JavaScript 版本改写而来，仅供大家学习参考<br/>
另外使用的时候能不能保留一下作者信息呀（就是菜单栏的 Made by Yuan_Tuo），谢~

## Demo
[已加密，暂不开放！](https://imwcr.cn/api/bdwp/)

## Setting
请在 `config.php` 中找：
```
define('BDUSS', '');
define('STOKEN', '');
define('IsCheckPassword', true);
define('Password', '请在这里填写密码啦！ヾ(≧▽≦*)o');
```
- 前两项替换成你自己的 SVIP 信息就行，获取 cookie 方法见视频 [BV1Yh411d7Gd](https://www.bilibili.com/video/BV1Yh411d7Gd)
- 第三项是是否需要密码的选项
- 第四项是首页需要输入的密码，但是如果第三项为 `false` 则无效
- 详细信息可见 `config.php` 的注释

## Thanks
- [baiduwp JavaScript 版](https://github.com/TkzcM/baiduwp "GitHub 项目")
- [PanDownload 网站](https://pandownload.com/ "PanDownload 网站")
- [KinhDown 客户端](https://t.me/kinhdown/ "KinhDown 客户端")
- [PNL 下载方式](https://www.lanzous.com/u/pnl "PNL 下载方式")

## New Changes
- 以下修改由 [LC](https://github.com/lc6464 "LC") 完成：2020-8-14
  - 配置、函数与程序分离（`php`）
  - 修复 POST 方法访问 `?download` 参数不齐全出错的问题
  - 修复未配置或配置了普通用户的 `BDUSS` 和 `STOKEN` 时显示空链接的问题
  - 样式、JavaScript 与页面分离（`前端`）
  - 优化数据传输
  - 优化用户体验
  - 优化打开文件夹的表现（原来直接提示不可用，现在跳转到百度网盘官方的分享页面）
  - 优化程序效率
  - 使用函数减少重复工作的代码量
  - 增加注释
  - 优化前端代码

## 坑或不确定
- `static/functions.js`
  - 36 行