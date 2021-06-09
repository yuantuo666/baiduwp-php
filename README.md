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
- http://www.dupan.cc/ （恶意篡改后台并加密，站长QQ33703259）

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
- 如果使用一段时间后失效，一般是**账号问题**或**服务器IP被baidu封了**；如果是方法失效，这个项目将关闭。
- 使用 `1.3.6` 版本及以前的站长，请及时更新到最新版本，老版本存在安全问题（在获取链接页面没有验证密码），可能导致SVIP账号被盗用。漏洞利用演示出于安全考虑不再对外展示。
- 处理下载限速方法
  - 尝试重新分享文件，部分文件可能出现奇怪的问题
  - 耐心等待baidu解封账号
  - 更换后台SVIP账号
  - 更换服务器IP
- 使用了较新的 JavaScript 和 CSS 特性，旧版浏览器对此的支持性很差，使用新版的现代浏览器才能正常使用！建议使用的浏览器：
  - `Microsoft Edge 88+` [点此访问 Edge 官网](https://www.microsoft.com/zh-cn/edge)
  - `Google Chrome 88+` [点此访问 Chrome 官网](https://www.google.cn/chrome/)
  - `Firefox 85+` [点此访问 Firefox 官网](https://www.firefox.com.cn/)

# 🔧Install & Setting
[**点此查看安装、配置、使用视频教程**](https://www.bilibili.com/video/BV1N5411A77n)

首先Clone项目或进入[Releases](https://github.com/yuantuo666/baiduwp-php/releases)下载项目文件。<br />
然后访问 `install.php` 文件并填写相关信息进行安装。<br />
如果使用数据库，则需要先点击 `检查数据库连接` 连接数据库，保证账号密码正确。<br />
最后点击提交即可。<br />

安装完成后可直接使用，站长可进入settings.php中进行相关设置。<br />
在**SVIP账号**中可设置**SVIP账号**的**BDUSS**和**STOKEN**，添加账号后记得进入**会员账号切换模式**将模式改成**顺序模式**或**轮换模式**。<br />

## 📦New Changes
- 当前版本：`2.1.7`
- 更新日期：2021-05-18
- 修改内容：
  - 💥新增功能
    - 新增 aria2 安卓端
    - 新增解析链接失效时间设置
    - 关于页面自动生成二维码
  - ⚠错误修复
    - 修复语言设置错误提示中转跳用户设置页面链接错误
    - 修复手机端推送 aria2 因文件夹不存在出错
  - ♻代码优化
    - 在新标签页打开 Motrix 首页
    - ws(s)链接检测支持 IPv6

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

因为作者及合作者都是学生，所以没有办法及时解决问题。<br />
如果遇到问题请先仔细阅读此文档和[以前的issue](https://github.com/yuantuo666/baiduwp-php/issues?q=is%3Aissue)，确实无法解决请提起issue，我们将很乐意为你解决🙂。<br />
所有的邮件我都会查看并回复的~

## 🔔Thanks
- [baiduwp JavaScript 版](https://github.com/TkzcM/baiduwp "GitHub 项目")
- [PanDownload 网站](https://pandownload.com/ "PanDownload 网站")
- [Bootstrap 深色模式](https://github.com/vinorodrigues/bootstrap-dark "bootstrap-dark 项目")
