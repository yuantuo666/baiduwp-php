# Baiduwp-PHP
PanDownload 网页复刻版，PHP 语言版<br/>
本项目是依据 [baiduwp](https://github.com/TkzcM/baiduwp "baiduwp") 的 JavaScript 版本改写而来，仅供大家学习参考<br/>
希望在使用时能够保留导航栏的 Made by Yuan_Tuo ，感谢！

因为作者开学了，所以项目将不再频繁更新。

## Blacklists
- http://byu5.cn/baiduwp/

以上网站使用本项目源码，未与作者联系而删除作者信息。
已经通知站长，请修改网站后联系我删除黑名单。

## Demo
[已加密，暂不开放！](https://imwcr.cn/api/bdwp/)

## Tips
- 使用了 `SESSION`，注意 **PHP 访问系统文件（夹）权限**问题
- 仅支持 **PHP 7 和 7+**！
- 一般情况下网页版不会出现问题，第一次使用就失败一般是**设置的问题**。
- 如果使用一段时间后失效，一般是**账号问题**或**服务器IP被baidu封了**；如果是方法失效，这个项目将关闭。
- 处理下载限速方法
  - 尝试重新分享文件，部分文件可能出现奇怪的问题
  - 耐心等待baidu解封账号
  - 更换后台SVIP账号
  - 更换服务器IP

## Setting
请在 `config.php` 中找：
```
define('BDUSS', '');
define('STOKEN', '');
define('IsCheckPassword', true);
define('Password', '请在这里填写密码啦！ヾ(≧▽≦*)o');
```
- 前两项填入你自己的 SVIP 信息就行，获取 cookie 方法见 [PD官网](https://pandownload.com/faq/cookie.html)
- 第三项是是否需要密码的选项
- 第四项是首页需要输入的密码，但是如果第三项为 `false` 则无效
- 详细信息可见 `config.php` 的注释

---

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

## About
#### JavaScript版作者
最开始Pandownload网页版复活版是由[TkzcM](https://github.com/TkzcM)大佬制作的，随后发布在[吾爱破解](https://www.52pojie.cn/thread-1238874-1-1.html)上。<br/>
B站UP主影视后期系统教学(uid250610800)分享了这个网站，分享的视频登上了热门，导致PanDL.Live大量用户涌入。随后在8.10这个网站就关闭了，原因是服务器成本太高，所以停止了服务。<br/>
但这位作者在github上开源了这份代码，于是我就下载下来研究，发现有不稳定的情况（不知道是不是我设置有问题），于是我就尝试把代码转写成PHP语言，发现效果好很多。

#### B站教程
随后我在B站发布了一个视频，介绍如何使用JavaScript。并在视频达到1000点赞后公布了PHP版的源码。<br/>
在8.22这个教程视频就被锁定了，B站给出的原因：该视频内容涉及不适宜内容，不予审核通过。如有疑问请通过稿件申诉进行反馈。<br/>
[原视频备份](https://v.youku.com/v_show/id_XNDc5MDExMzAyMA====.html)

#### LC优化版
[LC](https://github.com/lc6464 "LC")在我的邀请下，帮我完善了打开文件夹等一系列功能，并且制作了[优化版](https://github.com/lc6464/PanDownload-PHP-Optimized)和使用方法：[BV1dt4y1D7Cf](https://b23.tv/pfUrnp)

之后就有很多站长开始搭建PHP版，并在酷安、葫芦侠等平台传播开来。

#### 吾爱破解小工具
在8.25晚上吾爱破解上kemiok作者发布了制作的[度盘IDM高速下载](https://www.52pojie.cn/thread-1254032-1-1.html)小工具。<br/>
关于接口引用，因为论坛的规定，不能留下其他的网站网址，但联系作者得知他也很想去感谢那些站长。<br/>
