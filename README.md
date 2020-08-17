# baiduwp-php
PanDownload 网页复刻版，PHP 语言版<br/>
本项目是依据 [baiduwp](https://github.com/TkzcM/baiduwp "baiduwp") 的 JavaScript 版本改写而来，仅供大家学习参考<br/>
希望在使用时能够保留导航栏的 Made by Yuan_Tuo 和 Optimized by LC，感谢！
- 原作者 [Yuan_Tuo](https://github.com/yuantuo666 "Yuantuo")
- 由 [LC](https://github.com/lc6464 "LC") 优化

## Demo
[已加密，暂不开放！](https://imwcr.cn/api/bdwp/)

## 安装注意事项
- 使用了 `SESSION`，注意 **PHP 访问系统文件（夹）权限**问题
- 仅支持 **PHP 7 和 7+**！

## Setting
请在 `config.php` 中找：
```
define('BDUSS', '');
define('STOKEN', '');
define('IsCheckPassword', true);
define('Password', '请在这里填写密码啦！ヾ(≧▽≦*)o');
```
- 前两项填入你自己的 SVIP 信息就行，获取 cookie 方法见视频 [BV1Yh411d7Gd](https://www.bilibili.com/video/BV1Yh411d7Gd)
- 第三项是是否需要密码的选项
- 第四项是首页需要输入的密码，但是如果第三项为 `false` 则无效
- 详细信息可见 `config.php` 的注释

## Thanks
- [baiduwp JavaScript 版](https://github.com/TkzcM/baiduwp "GitHub 项目")
- [PanDownload 网站](https://pandownload.com/ "PanDownload 网站")
- [KinhDown 客户端](https://t.me/kinhdown/ "KinhDown 客户端")
- [PNL 下载方式](https://www.lanzous.com/u/pnl "PNL 下载方式")

## New Changes
- 当前版本：`1.3.5`
- 更新日期：2020-8-17
- 以下修改由 [LC](https://github.com/lc6464 "LC") 完成
  - 优化后端逻辑和效率
  - 优化代码
  - 优化错误时提示
  - 修复浏览器中点击下载链接，传递 Referer 导致概率性出错的问题

## 坑或不确定
- `static/functions.js`
  - 42 行