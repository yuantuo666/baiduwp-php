# Baiduwp-PHP

[ENGLISH](README.md) | [中文](README_ZH.md)

PanDownload 网页复刻版，PHP 语言版<br/>
本项目仅供学习参考，严禁商业用途<br/>

<div align="center"><a href="https://www.bilibili.com/video/BV1N5411A77n"><img src="https://i.loli.net/2021/04/04/9NJ2lC4T78o1XmZ.png" width="500"><br /><b>点此查看本项目安装、配置、使用视频教程</b></a></div>

## 🔎实现原理
通过curl获取网盘文件信息，处理后显示在网页中。通过api接口以及SVIP账号的Cookie(BDUSS)获取高速下载链接。<br/>
本质就是用会员账号获取下载地址并发送给访客。

<h2> 重要声明：本项目是 <a href="https://github.com/TkzcM/baiduwp">baiduwp</a> 的 PHP 语言实现；项目中所涉及的接口均来自<a href="https://pan.baidu.com/union">百度官方</a>，不涉及任何违法行为，本工具需要使用自己的百度网盘SVIP账号才能获取下载链接，代码全部开源，仅供学习参考；请不要将此项目用于商业用途，否则可能带来严重的后果。<br />
 1. <a href="https://wenshu.court.gov.cn/website/wenshu/181107ANFZ0BXSK4/index.html?docId=sdm5Qb3+eptZXYli7K6pxkuzRe++Lpf+6D1wFO17rcvApzo8iSsEbZ/dgBYosE2gsXAo9gkraFrIyNZhEOZTLcchR1OkgXb06zm4EqFo5gfXvKzSXfjCg7s3jTcG+ypG">中国裁判文书网《林蔚群提供侵入、非法控制计算机信息系统程序、工具罪一审刑事判决书》</a><br />
 2. <a href="https://wenshu.court.gov.cn/website/wenshu/181107ANFZ0BXSK4/index.html?docId=YBxnFgDqvuAqHdQyp/Sg8Q8PO/kX2Ej8TmtEOh9d2AdVpX9Qxi5YzJ/dgBYosE2gsXAo9gkraFrIyNZhEOZTLb1tEqCCr7c0irDVWK+bNT9AqupYNfRiqH1vVaFmakha">中国裁判文书网《北京度友科技有限公司等与罗庆等不正当竞争纠纷一审民事判决书》</a>
</h2>

![浅色及英文模式](https://s2.loli.net/2023/02/04/cs1EtFXpHDPS2AB.png)
![首页](https://s2.loli.net/2023/02/04/fJlru3yj6b4MVE1.png)
![文件列表](https://s2.loli.net/2023/02/04/hL2pDEyHQFb6BKR.png)
![解析详情](https://s2.loli.net/2023/02/04/GZBsmz6xgShjuA2.png)

## 📌Tips
- 使用了 `Curl`，使用前请确认安装了Curl及其PHP插件（导致问题的主要原因）
- 使用了 `SESSION`，注意 **PHP 访问系统文件（夹）权限** 问题
- 仅支持 **PHP 7 和 7+**！
- 一般情况下网页版不会出现问题，第一次使用就失败一般是**设置的问题**。
- 如果使用一段时间后失效，一般是**账号失效问题**（点击退出登录按钮会导致当此登录获取到的 Cookies 失效，更改密码会使当前帐号获取过的所有 Cookies 失效）或 **服务器 IP 被封禁**（在解析了大量文件后可能会出现此问题，阈值大约为几十TB），如果是获取下载链接的方法失效，此项目将会被关闭。
- 处理下载限速方法
  - 尝试重新分享文件，部分文件可能出现奇怪的问题，部分文件名也有可能导致问题
  - 耐心等待账号解封
  - 更换后台 SVIP 账号
  - 更换服务器 IP

# 🔧Install & Setting
[**点此查看安装、配置、使用视频教程**](https://www.bilibili.com/video/BV1N5411A77n)

1. 进入[Releases](https://github.com/yuantuo666/baiduwp-php/releases)下载项目文件
2. 访问 `install.php` 文件并填写相关信息进行安装
3. 如果使用数据库，则需要先点击 `检查数据库连接` 连接数据库，保证账号密码正确
4. 最后点击提交
5. 安装完成后可直接使用，可进入 `settings.php` 中进行相关设置
6. 获取 Cookie 可以通过浏览器直接获取（操作方法见视频） **获取完成后，请不要退出登录，这会使获取的 Cookies 失效**
7. 在**SVIP账号**中可设置**SVIP账号**的**BDUSS**和**STOKEN**，添加账号后记得进入**会员账号切换模式**将模式改成**顺序模式**或**轮换模式**

## 💡Contact
- 项目作者：Yuan_Tuo
- 作者首页：https://imwcr.cn/
- 作者邮箱：yuantuo666@gmail.com (不答复百度网盘相关问题)
- 合作者：LC @lc6464
  - [个人网站](https://lcwebsite.cn/ "LC的网站")
  - [联系](https://lcwebsite.cn/web/contact.aspx "联系 LC")

如果遇到问题请先 **仔细阅读此文档** 、查看[视频教程](https://www.bilibili.com/video/BV1N5411A77n)
以及查看[以前的议题](https://github.com/yuantuo666/baiduwp-php/issues)<br />

如果是**设置账号的 Cookies（BDUSS 和 STOKEN）**及**配置环境**等方面的问题，请自行解决。

## 🔔Thanks
- [baiduwp JavaScript 版](https://github.com/TkzcM/baiduwp "baiduwp 项目")
- [PanDownload 网站](https://pandownload.com/ "PanDownload 网站")
- [Bootstrap 深色模式](https://github.com/vinorodrigues/bootstrap-dark "bootstrap-dark 项目")
