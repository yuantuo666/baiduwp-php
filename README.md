# Baiduwp-PHP
PanDownload 网页复刻版，PHP 语言版<br/>
本项目是依据 [baiduwp](https://github.com/TkzcM/baiduwp "baiduwp")（JavaScript 语言版）改写而来，仅供大家学习参考<br/>
希望在使用时能够保留导航栏的 Made by Yuan_Tuo ，感谢！

![speed.gif](https://i.loli.net/2020/10/01/2mEqkClnPev8ORd.gif)

## Blacklists
- http://www.pojiewo.com/baidujx 1.4.2版本  注：此网站 **盗用** 其他网站的接口获取下载地址
- https://pan.biubixin.com/ 1.4.2版本
- http://xm08.cn/ 1.3.3版本
- http://47.98.113.215:1234/ 1.4.3版本
- https://www.bdwp.cf/ 1.0版本
- http://119.45.113.159/ 1.4.2版本
- http://39.106.129.224:8080/ 1.0版本
- http://59.110.124.211:9090/ 1.0版本
- https://panweb.tk/ 1.0版本
- http://pan.hongshiite.top/ 1.0版本

以上网站使用本项目源码，未与作者联系而删除作者信息。

### 我希望保留版权的目的只是想给那些想要学习这方面的人一个机会，再说保留这个对你自己又没有坏处。
### 源码我也没有收费，保留原作者版权也是MIT协议所规定的。这也是对作者的一种尊重，让作者有继续开发的动力。

## Donate
[捐赠作者](https://imwcr.cn/?donate)<br />
建议大家自己搭建自己用，搭建公益的没必要，只有投入没有回报。

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

# Setting
请在 `config.php` 中找到以下内容：
```
define('BDUSS', '①');
define('STOKEN', '②');
define('SVIP_BDUSS', '③');
define('IsCheckPassword', ④);
define('Password', '⑤');

define('APP_ID', '⑥');
define('DEBUG', ⑦);

define('USING_DB', ⑧);
define('DbConfig', array(
	"servername" => "⑨",
	"username" => "⑩",
	"password" => "⑪",
	"dbname" => "⑫",
	"dbtable" => "⑬"
));
```
- 【必填】请在①②填入`你自己的百度账号信息`*(SVIP也可)*，用于获取下载列表，获取 cookie 方法见 [PD官网](https://pandownload.com/faq/cookie.html)
- 【必填】在③中必须填入`SVIP的BDUSS`，用于获取下载链接，获取cookie方法同上。
- 请在④中选择是否需要密码(`TRUE`或者`FALSE`)
- 若开启了密码，请在⑤中设置是首页密码
- 在⑥中是获取文件的Dlink时使用的app_id
- 在⑦中是是否开启DEBUG调试模式
- 在⑧中是是否使用数据库，限制每日下载ip
- 在⑨-⑬是数据库设置

- 详细信息可见 `config.php` 的注释
---
### 演示案例
例如，你的BDUSS是 `123` ，STOKEN是 `456` ，SVIP的BDUSS是 `789` ，`开启` 密码并且设置为 `666` ，启用数据库。（数据库相关信息：服务器地址`localhost`、账号`root`、密码`root`、数据库名`bdwp`）<br />
那么应该将 `config.php` 中设置成以下的代码：
```
define('BDUSS', '123');
define('STOKEN', '456');
define('SVIP_BDUSS', '789');
define('IsCheckPassword', true);
define('Password', '666');

define('APP_ID', '25565');
define('DEBUG', false);

define('USING_DB', true);
define('DbConfig', array(
	"servername" => "localhost",
	"username" => "root",
	"password" => "root",
	"dbname" => "bdwp",
	"dbtable" => "bdwp"
));
```
---
### 数据库设置
要使用账号记录功能，请在`MySQL`中创建`bdwp`表
```
CREATE TABLE `bdwp` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`userip` TEXT NOT NULL COMMENT '用户ip',
	`filename` TEXT NOT NULL COMMENT '文件名',
	`size` TEXT NOT NULL COMMENT '文件大小',
	`md5` TEXT NOT NULL COMMENT '文件效验码',
	`path` TEXT NOT NULL COMMENT '文件路径',
	`server_ctime` TEXT NOT NULL COMMENT '文件创建时间',
	`realLink` TEXT NOT NULL COMMENT '文件下载地址',
	`ptime` datetime NOT NULL COMMENT '解析时间',
	PRIMARY KEY (`id`)
) ENGINE = MyISAM;
```
要使用SVIP自动切换功能，请在`MySQL`中创建`bdwp_svip`表
```
CREATE TABLE `bdwp_svip` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` TEXT NOT NULL COMMENT '账号名称',
	`svip_bduss` TEXT NOT NULL COMMENT '会员bduss',
	`add_time` datetime NOT NULL COMMENT '会员账号加入时间',
	`is_using` boolean NOT NULL COMMENT '是否正在使用(非零表示真)',
	PRIMARY KEY (`id`)
) ENGINE = MyISAM;
```
要使用黑/白名单功能，请在`MySQL`中创建`bdwp_ip`表
```
CREATE TABLE `bdwp_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ip地址',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '备注',
  `add_time` datetime NOT NULL COMMENT '白黑名单添加时间',
  `type` tinyint(4) NOT NULL COMMENT '状态(0:允许,-1:禁止)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
```
## Demo
[暂不开放](http://imwcr.cn/api/bdwp/)<br />
演示服务器曾在 2020-09-30 19:17:55 遭到20GbpsDDoS攻击。<br />
因站长学习紧张加上精力有限，演示站没有时间维护，故暂时关闭。
## New Changes
- 当前版本：`1.4.3`
- 更新日期：2020-10-20
- 修改内容
  - 后台增加MySQL数据库，保存8小时内解析文件。
  - 限制同一IP及设备的解析次数。

# About
#### baiduwp JavaScript版
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
在9.27号百度网盘更新了新的V7.0.5 Windows版本，其他开发者开发的黑解算法失效，此项目不受影响。

#### baiduwp Spring Boot 版
作者 muzi9527 以本项目为蓝本，改写了[baiduwp-springboot](https://github.com/muzi9527/baiduwp-springboot)（Spring Boot语言版）。

## Thanks
- [baiduwp JavaScript 版](https://github.com/TkzcM/baiduwp "GitHub 项目")
- [PanDownload 网站](https://pandownload.com/ "PanDownload 网站")
