# Baiduwp-PHP

[ENGLISH](README.md) | [中文](README_ZH.md)

PanDownload 网页复刻版，PHP 语言版<br/>
本项目仅供学习参考，严禁商业用途<br/>

## 🔎 实现原理
通过curl获取网盘文件信息，处理后显示在网页中。通过api接口以及SVIP账号的Cookie(BDUSS)获取高速下载链接。<br/>
本质就是用会员账号获取下载地址并发送给访客。

<h2> 重要声明：本项目是 <a href="https://github.com/TkzcM/baiduwp">baiduwp</a> 的 PHP 语言实现；项目中所涉及的接口均来自<a href="https://pan.baidu.com/union">百度官方</a>，不涉及任何违法行为，本工具需要使用自己的百度网盘SVIP账号才能获取下载链接，代码全部开源，仅供学习参考；请不要将此项目用于商业用途，否则可能带来严重的后果。<br />
 1. 中国裁判文书网《林蔚群提供侵入、非法控制计算机信息系统程序、工具罪一审刑事判决书》<br />
 2. 中国裁判文书网《北京度友科技有限公司等与罗庆等不正当竞争纠纷一审民事判决书》
</h2>

![浅色首页](https://s2.loli.net/2023/04/04/yegBh8HXaNCqixb.png)
![深色首页](https://s2.loli.net/2023/04/04/Ff1ub4MJxVsHYhZ.png)
![文件列表](https://s2.loli.net/2023/04/04/4XOrj9xlFYqSyhw.png)
![解析详情](https://s2.loli.net/2023/04/04/aVPoxJ52zCZLpIK.png)
![后台页面](https://s2.loli.net/2023/04/04/dzvxNqO82WrM4lQ.png)

# 🔧 安装及设置
~~[**点此查看安装、配置、使用视频教程**](https://www.bilibili.com/video/BV1N5411A77n)~~ Outdated

## 宝塔面板 / 虚拟主机安装
[AFF] 市面上虚拟主机参数参差不齐，经测试 [雨云](https://www.rainyun.cc/?ref=MjQyNDk=) 可完美运行本程序(香港EP二代 入门版, 7元/月)。

### 虚拟主机 EasyPanel 面板安装
1. 进入 [Releases](https://github.com/yuantuo666/baiduwp-php/releases) 下载项目文件 `baiduwp-php-v?.?.?.zip`
2. 上传到 EasyPanel 面板 `wwwroot` 目录并解压
3. 在配置域名页面中，绑定域名到 `/wwwroot/public` 目录下
4. 进入网站 `http://<网站域名>/install`，按照页面提示进行安装
5. 进入 `http://<网站域名>/admin` 的系统设置页面中设置 普通 和 SVIP账号 的 Cookie。

### 宝塔面板安装
1. 进入 [Releases](https://github.com/yuantuo666/baiduwp-php/releases) 下载项目文件 `baiduwp-php-v?.?.?.zip`
2. 在宝塔面板创建网站，上传项目文件到网站根目录并解压
3. 在 网站 —— 站点修改 —— 子目录绑定 中，绑定网站域名到子目录 `public`
4. 在 网站 —— 站点修改 —— 伪静态 中，选择 `thinkphp` 并保存。
5. 进入网站 `http://<网站域名>/install`，按照页面提示进行安装
6. 进入 `http://<网站域名>/admin` 的系统设置页面中设置 普通 和 SVIP账号 的 Cookie。

## Docker 安装
### 使用 SQLite 数据库 / 不使用数据库
1. 安装 docker
2. 执行下面的命令
```
docker pull yuantuo666/baiduwp-php
docker run -d -p 8080:8000 yuantuo666/baiduwp-php
```
```
== 相关信息 ==
启动后服务将在 http://服务器IP:8080/ 运行
如需修改端口，可修改上方命令

== 安装时配置 ==
请 关闭数据库功能 或 切换至 SQLite 数据库

== 设置页面 ==
http://服务器IP:8080/admin
```

### 使用 MySQL 数据库
1. 安装 docker
2. 执行下面的命令
```
docker pull mysql
docker network create --subnet 172.28.0.0/16 mysql-network
docker run -d -e MYSQL_ROOT_PASSWORD="root" --network mysql-network --ip 172.28.0.2 mysql

docker pull yuantuo666/baiduwp-php
docker run -d --network mysql-network --ip 172.28.0.3 -p 8080:8000 yuantuo666/baiduwp-php
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

== 设置页面 ==
http://服务器IP:8080/admin
```

### 自行构建
```
git clone https://github.com/yuantuo666/baiduwp-php
cd baiduwp-php
docker build -t baiduwp-php .
docker run -d -p 80:8000 baiduwp-php

docker exec -it <容器ID> /bin/bash
tar -cvf /app.tar /app
cp /app.tar /app/public/app.tar
wget http://<HOST>/app.tar
```

## 📌 使用提示
- 使用了 `Curl`，使用前请确认 **安装了 Curl** 及 **启用其 PHP 插件**
- 使用了 `SESSION`，注意 **PHP 访问系统文件（夹）权限** 问题
- 仅支持 **PHP 7 和 7+**！
- 本项目使用的接口容易导致账号限速，参见 [#113](https://github.com/yuantuo666/baiduwp-php/issues/113)
- 需要配置两个 `完整 Cookie`(普通账号和SVIP账号均可) 才可以获取下载链接，获取方法需抓包。
  - 获取 Cookie 参考 [图文教程](https://blog.imwcr.cn/2022/11/24/%e5%a6%82%e4%bd%95%e6%8a%93%e5%8c%85%e8%8e%b7%e5%8f%96%e7%99%be%e5%ba%a6%e7%bd%91%e7%9b%98%e7%bd%91%e9%a1%b5%e7%89%88%e5%ae%8c%e6%95%b4-cookie/)
  - 获取之后请勿退出账号或修改密码，否则 Cookie 会失效

## 📚 进一步阅读
- [更新日志](docs/CHANGELOG.md)
- [关于这个项目](docs/About.md)
- [API 文档](docs/API.md)
  - 自 `3.0.0` 版本开始，本项目支持 API 接口。核心功能如获取文件列表、下载地址等均可通过 API 完成，具体请查看 [API 文档](docs/API.md)。

## 📝 TODO
- [ ] 多语言完善
- [x] 逐步重构代码

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
- [ThinkPHP](https://github.com/top-think/think)