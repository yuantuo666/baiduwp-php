# Baiduwp-PHP
PanDownload 网页复刻版，PHP 语言版<br/>
本项目是依据 [baiduwp](https://github.com/TkzcM/baiduwp "baiduwp")（JavaScript 语言版）改写而来，仅供大家学习参考<br/>
希望在使用时能够保留导航栏的 Made by Yuan_Tuo ，感谢！

![speed.gif](https://i.loli.net/2020/10/01/2mEqkClnPev8ORd.gif)

## Warning!!
http://www.dupan.cc/ （站长QQ33703259） 所发布的源码已被篡改，添加了后台并加密，添加的bduss会被上传网站后台，请勿使用<br />

现在已经公布了所有源码，可以通过github的commits查看以前的源码，从未添加过后台，也没有加密。<br />
dupan.cc所提供的源码已经被dupan.cc站长恶意修改并加密。使用篡改的源码，在后台添加的bduss会被上传dupan.cc服务器，请勿使用！！！！<br />
目前此网站已无法访问。

## Donate
[捐赠作者](https://imwcr.cn/?donate)<br />

## Blacklists
- http://www.pojiewo.com/baidujx 1.4.2版本  注：此网站 **盗用** 其他网站的接口获取下载地址
- https://pan.biubixin.com/ 1.4.2版本
- http://xm08.cn/ 1.3.3版本
- http://47.98.113.215:1234/ 1.4.3版本
- https://www.bdwp.cf/ 1.0版本
- http://39.106.129.224:8080/ 1.0版本
- http://59.110.124.211:9090/ 1.0版本
- https://panweb.tk/ 1.0版本
- http://pan.hongshiite.top/ 1.0版本

以上网站使用本项目源码，未与作者联系而删除作者信息。

## Tips
- 使用了 `SESSION`，注意 **PHP 访问系统文件（夹）权限**问题
- 仅支持 **PHP 7 和 7+**！
- 一般情况下网页版不会出现问题，第一次使用就失败一般是**设置的问题**。
- 如果使用一段时间后失效，一般是**账号问题**或**服务器IP被baidu封了**；如果是方法失效，这个项目将关闭。
- 使用 `1.3.6` 版本及以前的站长，请及时更新到最新版本，老版本存在安全问题（在获取链接页面没有验证密码），可能导致SVIP账号被盗用。[漏洞利用演示](https://i.loli.net/2020/08/29/hdjEKGzTZBu6yQI.gif)
- 处理下载限速方法
  - 尝试重新分享文件，部分文件可能出现奇怪的问题
  - 耐心等待baidu解封账号
  - 更换后台SVIP账号
  - 更换服务器IP
- 使用了较新的 JavaScript 和 CSS 特性，旧版浏览器对此的支持性很差，使用新版的现代浏览器才能正常使用！建议使用的浏览器：
  - `Microsoft Edge 88+` [点此访问 Edge 官网](https://www.microsoft.com/zh-cn/edge)
  - `Google Chrome 88+` [点此访问 Chrome 官网](https://www.google.cn/chrome/)
  - `Firefox 85+` [点此访问 Firefox 官网](https://www.firefox.com.cn/)

# Setting
首先Clone项目或进入[Releases](https://github.com/yuantuo666/baiduwp-php/releases)下载项目文件。<br />
然后访问 `install.php` 文件并填写相关信息。<br />
如果使用数据库，则需要先点击 `检查数据库连接` 连接数据库，保证账号密码正确。<br />
最后点击提交即可。<br />

## Demo
[暂不开放](http://imwcr.cn/api/bdwp/)<br />
因站长学习紧张加上精力有限，演示站没有时间维护，故暂时关闭。

## New Changes
- 当前版本：`1.4.6`
- 更新日期：2021-02-10
- 修改内容
  - 由LC优化代码

# About
#### baiduwp JavaScript版
最开始Pandownload网页版复活版是由[TkzcM](https://github.com/TkzcM)大佬制作的，随后发布在[吾爱破解](https://www.52pojie.cn/thread-1238874-1-1.html)上。<br/>
B站UP主影视后期系统教学(uid250610800)分享了这个网站，分享的视频登上了热门，导致PanDL.Live大量用户涌入。随后在8.10这个网站就关闭了。<br/>
但这位作者在github上开源了这份代码，于是我就下载下来研究，发现有不稳定的情况（不知道是不是我设置有问题），于是我就尝试把代码转写成PHP语言，发现效果好很多。

#### B站教程
随后我在B站发布了一个视频，介绍如何使用JavaScript。并在视频达到1000点赞后公布了PHP版的源码。<br/>
在8.22这个教程视频就被锁定了，B站给出的原因：该视频内容涉及不适宜内容，不予审核通过。如有疑问请通过稿件申诉进行反馈。<br/>
[原视频备份](https://v.youku.com/v_show/id_XNDc5MDExMzAyMA====.html)

#### LC优化版
[LC](https://github.com/lc6464 "LC")在我的邀请下，帮我完善了打开文件夹等一系列功能，并且制作了[优化版](https://github.com/lc6464/PanDownload-PHP-Optimized)和使用方法：[BV1dt4y1D7Cf](https://b23.tv/pfUrnp)

之后就有很多站长开始搭建PHP版，并在酷安、葫芦侠等平台传播开来。

##### 来自LC本人的补充
> 优化版的功能以及远远落后于原版的更新了，而部分功能的实现又与原版不同，没有办法直接拉取请求，故不再维护，并存档了此仓库。<br/>
> 目前[复刻的仓库](https://github.com/lc6464/baiduwp-php)仍在继续维护，并用来提交拉取请求。


#### 吾爱破解小工具
在8.25晚上吾爱破解上kemiok作者发布了制作的[度盘IDM高速下载](https://www.52pojie.cn/thread-1254032-1-1.html)小工具。<br/>
关于接口引用，因为论坛的规定，不能留下其他的网站网址，但联系作者得知他也很想去感谢那些站长。<br/>

#### 百度网盘算法更新
在9.27号百度网盘更新了新的V7.0.5 Windows版本，其他开发者开发的黑解算法失效，此项目不受影响。

#### baiduwp Spring Boot 版
作者 muzi9527 以本项目为蓝本，改写了[baiduwp-springboot](https://github.com/muzi9527/baiduwp-springboot)（Spring Boot语言版）。

## Thanks
- [baiduwp JavaScript 版](https://github.com/TkzcM/baiduwp "GitHub 项目")
- [PanDownload 网站](https://pandownload.com/ "PanDownload 网站")
