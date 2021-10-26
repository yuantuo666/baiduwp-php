# Baiduwp-PHP
PanDownload 网页复刻版，PHP 语言版<br/>
本项目仅供大家学习参考，严禁商业用途

由于百度网盘修改分享页面JavaScript代码，导致所有旧版本失效，请更新至 `2.1.4` 或更新版本体验本项目。<br/>
详细信息参见[#93](https://github.com/yuantuo666/baiduwp-php/issues/93)

<div align="center"><a href="https://www.bilibili.com/video/BV1N5411A77n"><img src="https://i.loli.net/2021/04/04/9NJ2lC4T78o1XmZ.png" width="500"><br /><b>点此查看本项目安装、配置、使用视频教程</b></a></div>

## 🔎实现原理
通过curl获取网盘文件信息，处理后显示在网页中。通过api接口以及SVIP账号的Cookie(BDUSS)获取高速下载链接。<br/>
本质就是用会员账号获取下载地址并发送给访客。

📢在使用时请保留导航栏的 Made by Yuan_Tuo ，感谢！

📢欢迎各位转发本项目到各大论坛，但请一定要标注原地址！

![speed.gif](https://i.loli.net/2021/04/04/pRD1hA8rKLeEbn9.gif)

## 💻Demo
[暂不开放](http://imwcr.cn/api/bdwp/)<br />
因站长学习紧张加上精力有限，演示站没有时间维护，故暂时关闭。

## 🚧Blacklists
- http://www.dupan.cc/ （恶意篡改后台并加密，站长QQ33703259，[网站快照](https://web.archive.org/web/20210125182649/http://www.dupan.cc/)）

- http://www.pojiewo.com/baidujx 1.4.2版本  注：此网站 **盗用** 其他网站的接口获取下载地址
<!-- - https://pan.xiaoshuyun.cn/ 1.4.3版本 无密码 -->
<!-- - https://bd.pkqjsq.top/ 1.4.3版本 -->
<!-- - http://pan.0ddt.com/ 1.0版本 -->
<!-- - https://129.146.174.245/ 1.4.5版本 -->
<!-- - https://pan.lie01.com/ 1.4.3版本 -->
<!-- - https://www.bdwp.cf/ 1.4.3版本 -->

以上网站使用本项目源码，未与作者联系而删除作者信息。<br />
版权信息可添加**Github项目地址**或**我个人主页地址**，内容可自定，但访客**必须可见**。<br />
**那些把文字颜色和背景改成一样的站长，有意思吗？**

## 📌Tips
- 使用了 `Curl`，使用前请确认安装了Curl及其PHP插件
- 使用了 `SESSION`，注意 **PHP 访问系统文件（夹）权限** 问题
- 仅支持 **PHP 7 和 7+**！
- 一般情况下网页版不会出现问题，第一次使用就失败一般是**设置的问题**。
- 如果使用一段时间后失效，一般是**账号失效问题**（点击退出登录按钮会导致当此登录获取到的 Cookies 失效，更改密码会使当前帐号获取过的所有 Cookies 失效）或 **服务器 IP 被封禁**（在解析了大量文件后可能会出现此问题，阈值大约为几十TB），如果是获取下载链接的方法失效，此项目将会被关闭。
- 处理下载限速方法
  - 尝试重新分享文件，部分文件可能出现奇怪的问题，部分文件名也有可能导致问题
  - 耐心等待账号解封
  - 更换后台 SVIP 账号
  - 更换服务器 IP
- 使用了较新的 JavaScript 和 CSS 特性，旧版浏览器对此的支持性很差，使用新版的现代浏览器才能正常使用！建议使用的浏览器：
  - `Microsoft Edge 91+` [点此访问 Edge 官网](https://www.microsoft.com/zh-cn/edge)
  - `Google Chrome 91+` [点此访问 Chrome 官网](https://www.google.cn/chrome/)
  - `Firefox 90+` [点此访问 Firefox 官网](https://www.firefox.com.cn/)

# 🔧Install & Setting
[**点此查看安装、配置、使用视频教程**](https://www.bilibili.com/video/BV1N5411A77n)

首先Clone项目或进入[Releases](https://github.com/yuantuo666/baiduwp-php/releases)下载项目文件。<br />
然后访问 `install.php` 文件并填写相关信息进行安装。<br />
如果使用数据库，则需要先点击 `检查数据库连接` 连接数据库，保证账号密码正确。<br />
最后点击提交即可。<br />

安装完成后可直接使用，站长可进入settings.php中进行相关设置。<br />
获取 Cookie 可以通过浏览器直接获取（操作方法见视频），或者通过这个浏览器插件获取：[GetBaiduPanCookies](https://github.com/dylanbai8/GetBaiduPanCookies)。<br />
在**SVIP账号**中可设置**SVIP账号**的**BDUSS**和**STOKEN**，添加账号后记得进入**会员账号切换模式**将模式改成**顺序模式**或**轮换模式**。<br />

## 📦New Changes
- 当前版本: `2.2.0`
- 更新日期：2021-09-21
- 修改内容：
  - 💥新增功能
    - ✨支持老版本链接兼容加载
    - ✨会员账号批量导入支持设置 STOKEN 以及独自用户名
      - 格式：BDUSS----STOKEN----账号名称
    - 增加默认语言选项
    - 更新提示增加展开动画，且可以取消提示
    - 语言切换支持苹果 Safari 浏览器 zh-cn 标识
    - 增加安装提示
  - ⚠错误修复
    - 修复加载或切换页面白屏问题
    - 修复深色模式下首页输入框高度错误
    - settings.php 以及 install.php 页面更新提示与 ready.js 同步
    - 修复二维码内容无法加载问题
    - 安装页面设置站点默认语言 bug 修复
  - ♻代码优化
    - 请求静态文件时会带上版本号，减少更新时的因缓存导致的bug
    - 小屏幕不展示二维码
      - 手机端展示二维码时会挡住下载链接，导致无法复制
    - 获取文件失败提示内容修改


[查看更多](Update.md)

## 💴Donate
<img src="https://imwcr.cn/resources/img/donate.jpg" width="400"/>

[捐赠作者](https://imwcr.cn/?donate)

## 💡Contact
- 项目作者：Yuan_Tuo
- 作者邮箱：yuantuo666@gmail.com
- 作者首页：https://imwcr.cn/
- Telegram：
  - [@yuantuo666](https://t.me/yuantuo666)
  - [Telegram频道](https://t.me/baiduwp_php)
- 合作者：LC @lc6464
  - [个人网站](https://lcwebsite.cn/ "LC的网站")
  - [联系](https://lcwebsite.cn/web/contact.aspx "联系 LC")

**作者及合作者都是学生，因未来一段时间课余时间很少，对此项目维护将会减少。** #130<br />
如果遇到问题请先 **仔细阅读此文档** 、查看[视频教程](https://www.bilibili.com/video/BV1N5411A77n)
以及查看[以前的议题](https://github.com/yuantuo666/baiduwp-php/issues)<br />

如果是**设置账号的 Cookies（BDUSS 和 STOKEN）**及**配置环境**等方面的问题，请尽可能自行解决！[Google](https://www.google.com/ "谷歌") [Bing](https://cn.bing.com/ "必应")<br />


## 🔔Thanks
- [baiduwp JavaScript 版](https://github.com/TkzcM/baiduwp "baiduwp 项目")
- [PanDownload 网站](https://pandownload.com/ "PanDownload 网站")
- [Bootstrap 深色模式](https://github.com/vinorodrigues/bootstrap-dark "bootstrap-dark 项目")
