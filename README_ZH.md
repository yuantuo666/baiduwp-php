# Baiduwp-PHP

[ENGLISH](README.md) | [中文](README_ZH.md)

PanDownload 网页复刻版，PHP 语言版<br/>
本项目仅供学习参考，严禁商业用途<br/>

<div align="center"><a href="https://www.bilibili.com/video/BV1N5411A77n"><img src="https://i.loli.net/2021/04/04/9NJ2lC4T78o1XmZ.png" width="500" alt="视频教程"><br /><b>点此查看本项目安装、配置、使用视频教程</b></a></div>

## 🔎 实现原理
通过curl获取网盘文件信息，处理后显示在网页中。通过api接口以及SVIP账号的Cookie(BDUSS)获取高速下载链接。<br/>
本质就是用会员账号获取下载地址并发送给访客。

<h2> 重要声明：本项目是 <a href="https://github.com/TkzcM/baiduwp">baiduwp</a> 的 PHP 语言实现；项目中所涉及的接口均来自<a href="https://pan.baidu.com/union">百度官方</a>，不涉及任何违法行为，本工具需要使用自己的百度网盘SVIP账号才能获取下载链接，代码全部开源，仅供学习参考；请不要将此项目用于商业用途，否则可能带来严重的后果。<br />
 1. <a href="https://wenshu.court.gov.cn/website/wenshu/181107ANFZ0BXSK4/index.html?docId=sdm5Qb3+eptZXYli7K6pxkuzRe++Lpf+6D1wFO17rcvApzo8iSsEbZ/dgBYosE2gsXAo9gkraFrIyNZhEOZTLcchR1OkgXb06zm4EqFo5gfXvKzSXfjCg7s3jTcG+ypG">中国裁判文书网《林蔚群提供侵入、非法控制计算机信息系统程序、工具罪一审刑事判决书》</a><br />
 2. <a href="https://wenshu.court.gov.cn/website/wenshu/181107ANFZ0BXSK4/index.html?docId=YBxnFgDqvuAqHdQyp/Sg8Q8PO/kX2Ej8TmtEOh9d2AdVpX9Qxi5YzJ/dgBYosE2gsXAo9gkraFrIyNZhEOZTLb1tEqCCr7c0irDVWK+bNT9AqupYNfRiqH1vVaFmakha">中国裁判文书网《北京度友科技有限公司等与罗庆等不正当竞争纠纷一审民事判决书》</a>
</h2>

![浅色首页](https://s2.loli.net/2023/04/04/yegBh8HXaNCqixb.png)
![深色首页](https://s2.loli.net/2023/04/04/Ff1ub4MJxVsHYhZ.png)
![文件列表](https://s2.loli.net/2023/04/04/4XOrj9xlFYqSyhw.png)
![解析详情](https://s2.loli.net/2023/04/04/aVPoxJ52zCZLpIK.png)
![后台页面](https://s2.loli.net/2023/04/04/dzvxNqO82WrM4lQ.png)

# 🔧 安装及设置
[**点此查看安装、配置、使用视频教程**](https://www.bilibili.com/video/BV1N5411A77n)

## 宝塔面板 / 虚拟主机安装
[AFF] 市面上虚拟主机参数参差不齐，经测试 [雨云](https://www.rainyun.cc/?ref=MjQyNDk=) 可完美运行本程序(香港EP二代 入门版, 7元/月)。
1. 进入 [Releases](https://github.com/yuantuo666/baiduwp-php/releases) 下载项目文件 解压到对应目录
2. 访问 `/install.php` 文件并填写相关信息进行安装
3. 最后点击提交即可，需进行设置可网页访问 `/settings.php` （需启用数据库功能）

## Docker 安装
### 不使用MySQL数据库
1. 安装 docker
2. 执行下面的命令
```
docker pull yuantuo666/baiduwp-php
docker run -p 8080:80 yuantuo666/baiduwp-php
```
```
== 相关信息 ==
启动后服务将在 http://服务器IP:8080/ 运行
如需修改端口，可修改上方命令

== 安装时配置 ==
请关闭数据库功能

== 设置页面（需启用数据库功能） ==
http://服务器IP:8080/settings.php
```

### 使用MySQL数据库
1. 安装 docker
2. 执行下面的命令
```
docker pull mysql
docker network create --subnet 172.28.0.0/16 mysql-network
docker run -e MYSQL_ROOT_PASSWORD="root" --network mysql-network --ip 172.28.0.2 mysql

docker pull yuantuo666/baiduwp-php
docker run --network mysql-network --ip 172.28.0.3 -p 8080:80 yuantuo666/baiduwp-php
```
```
== 相关信息 ==
启动后服务将在 http://服务器IP:8080/ 运行
如需修改端口，可修改上方命令

== 安装时配置 ==
数据库地址 172.28.0.2
数据库用户名 root
数据库密码 root
数据库名 bdwp

== 设置页面（需启用数据库功能） ==
http://服务器IP:8080/settings.php
```

## 📌 使用提示
- 使用了 `Curl`，使用前请确认 **安装了 Curl** 及 **启用其 PHP 插件**
- 使用了 `SESSION`，注意 **PHP 访问系统文件（夹）权限** 问题
- 仅支持 **PHP 7 和 7+**！
- 本项目使用的接口容易导致账号限速，参见 [#113](https://github.com/yuantuo666/baiduwp-php/issues/113)
- 需要配置 `完整 Cookie`(普通账号和SVIP账号均可) + `BDUSS, STOKEN`(需SVIP账号) 才可以获取下载链接，获取方法需抓包。
  - 获取 Cookie 参考 [图文教程](https://blog.imwcr.cn/2022/11/24/%e5%a6%82%e4%bd%95%e6%8a%93%e5%8c%85%e8%8e%b7%e5%8f%96%e7%99%be%e5%ba%a6%e7%bd%91%e7%9b%98%e7%bd%91%e9%a1%b5%e7%89%88%e5%ae%8c%e6%95%b4-cookie/)
  - `BDUSS` 和 `STOKEN` 是 `Cookie` 中的两个参数。可以通过 `抓包 + 手动截取` 获得，或参考 [伪PD教程](https://pandownload.net/faq/cookie.html)。
  - `BDUSS` 长度 192，其不包含开头的 `BDUSS=`；`STOKEN` 长度 64，其不包含开头的 `STOKEN=`。

## 📚 进一步阅读
- [更新日志](docs/CHANGELOG.md)
- [关于这个项目](docs/About.md)
- [API 文档](docs/API.md)
  - 自 `3.0.0` 版本开始，本项目支持 API 接口。核心功能如获取文件列表、下载地址等均可通过 API 完成，具体请查看 [API 文档](docs/API.md)。

## 📝 TODO
- [ ] 多语言完善
- [ ] 逐步重构代码

## 💡 联系作者
- 项目作者：Yuan_Tuo
- 作者首页：https://imwcr.cn/
- 作者邮箱：yuantuo666@gmail.com (不答复百度网盘相关问题)
- 合作者：LC @lc6464
  - [个人网站](https://lcwebsite.cn/ "LC的网站")
  - [联系](https://lcwebsite.cn/web/contact.aspx "联系 LC")

如果遇到问题请先 **仔细阅读此文档** 、查看[视频教程](https://www.bilibili.com/video/BV1N5411A77n)
以及查看[以前的议题](https://github.com/yuantuo666/baiduwp-php/issues)<br />
如果还是无法解决，请在 [Issues](https://github.com/yuantuo666/baiduwp-php/issues) 中按模板提出问题，我会尽快回复。

## 📃 License
[MIT](LICENSE)

## 🔔 Thanks
- [baiduwp JavaScript 版](https://github.com/TkzcM/baiduwp "baiduwp 项目")
- [PanDownload 网站](https://pandownload.com/ "PanDownload 网站")
- [Bootstrap 深色模式](https://github.com/vinorodrigues/bootstrap-dark "bootstrap-dark 项目")
