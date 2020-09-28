# Baiduwp-PHP
PanDownload 网页复刻版，PHP 语言版<br/>
本项目是依据 [baiduwp](https://github.com/TkzcM/baiduwp "baiduwp") 的 JavaScript 版本改写而来，仅供大家学习参考<br/>
希望在使用时能够保留导航栏的 Made by Yuan_Tuo ，感谢！

### 我希望保留版权的目的只是想给那些想要学习这方面的人一个机会，再说保留这个对你自己又没有坏处。
### 源码我也没有收费，保留原作者版权也是MIT协议所规定的。这也是对作者的一种尊重，让作者有继续开发的动力。

目前 `1.4.2` 版本比较稳定，基本不会更新。

注意！使用 `1.3.6` 版本及以前的站长，请及时更新到最新版本，老版本存在安全问题，可能导致SVIP账号被盗用。[漏洞利用演示](https://i.loli.net/2020/08/29/hdjEKGzTZBu6yQI.gif)

## Blacklists
- http://c.duozy.cn/ 版本1.3.5 

以上网站使用本项目源码，未与作者联系而删除作者信息。
已经通知站长，请修改网站后联系我删除黑名单。

## Demo
[前往演示地址](https://imwcr.cn/api/bdwp/)<br />
为方便测试程序可用性，现开放demo演示。<br />
每个IP每天有一次免费解析机会，解析后的下载地址会在数据库中保存8小时，在8小时内再次解析不消耗次数。<br />
愿意捐赠SVIP账号的同学可以邮件联系yuantuo666@gmail.com，将会在首页展示捐赠者。

## Donate
[捐赠作者](https://imwcr.cn/?donate)<br />
建议大家自己搭建自己用，搭建公益的没必要，只有投入没有回报。<br />
百度网盘早就被爆存在漏洞，可以不需要SVIP账号直接解析，更加没有建公益的必要。

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
define('SVIP_BDUSS', '');
define('IsCheckPassword', true);
define('Password', '请在这里填写密码啦！ヾ(≧▽≦*)o');
```
- 前两项填入`你自己的百度账号信息`*(SVIP也可)*，用于获取下载列表，获取 cookie 方法见 [PD官网](https://pandownload.com/faq/cookie.html)
- 第三项必须填入`SVIP的BDUSS`，用于获取下载链接，获取cookie方法同上。
- 第四项是是否需要密码的选项
- 第五项是首页需要输入的密码，但是如果第三项为 `false` 则无效
- 详细信息可见 `config.php` 的注释

---

例如，你的BDUSS是 `123` ，STOKEN是 `456` ，SVIP的BDUSS是 `789` ，开启密码并且设置为 `666` ，那么应该将 `config.php` 中设置成以下的代码：

```
define('BDUSS', '123');
define('STOKEN', '456');
define('SVIP_BDUSS', '789');
define('IsCheckPassword', true);
define('Password', '666');
```

## Thanks
- [baiduwp JavaScript 版](https://github.com/TkzcM/baiduwp "GitHub 项目")
- [PanDownload 网站](https://pandownload.com/ "PanDownload 网站")
- [KinhDown 客户端](https://t.me/kinhdown/ "KinhDown 客户端")
- [PNL 下载方式](https://www.lanzous.com/u/pnl "PNL 下载方式")
- [LC 优化版](https://github.com/lc6464 "LC")

## New Changes
- 当前版本：`1.4.2`
- 更新日期：2020-8-29
- 修改内容
  - 列表页面新增超时提醒，5min后弹窗提示。
  - 修复在线播放功能，在设置UA情况下可以播放50MB以上文件。
  - 优化代码，删除打开文件夹每次查询密码是否正确代码。
  - 加入运行时间计算，在控制台中可以查看。
  - 将SVIP的BDUSS分离开，便于后期维护。
  - 隐藏旧链接显示的sharelinkXXX-XXX文件夹（此文件夹无法正常打开）。
  - 增加调试模式，便于反馈问题。
  - 增加自动从分享文本中提取验证密码功能。

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

#### 百度网盘算法更新
在9.27号百度网盘更新了新的V7.0.5Windows版本，其他开发者开发的黑解算法失效，此项目不受影响。![Screenshot_20200928_231021.jpg](https://i.loli.net/2020/09/28/tfJpGDYQswF2Ul8.jpg)
