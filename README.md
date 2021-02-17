# Baiduwp-PHP
PanDownload 网页复刻版，PHP 语言版<br/>
本项目仅供大家学习参考，严禁商业用途

## 🔎实现原理
通过curl获取网盘文件信息，处理后显示在网页中。通过api接口以及SVIP账号的Cookie(BDUSS)获取高速下载链接。<br/>
本质就是用会员账号获取下载地址并发送给访客。

📢在使用时请保留导航栏的 Made by Yuan_Tuo ，感谢！

📢欢迎各位转发本项目到各大论坛，但请一定要标注原地址！

![speed.gif](https://i.loli.net/2020/10/01/2mEqkClnPev8ORd.gif)

## 💴Donate
[捐赠作者](https://imwcr.cn/?donate)

## 🚧Blacklists
<!-- - http://down.5nb.me/ 1.4.5版本（站长拒不修改） -->
- https://pan.xiaoshuyun.cn/ 1.4.3版本 无密码
- https://pan.qiafan.vip/ 1.4.5版本 无密码
- http://www.dupan.cc/ （恶意篡改后台并加密，站长QQ33703259）
<!-- - https://bd.fmvp.cc/ 1.4.5版本 无密码 -->

- http://www.pojiewo.com/baidujx 1.4.2版本  注：此网站 **盗用** 其他网站的接口获取下载地址
- https://202.61.130.143/ 1.4.2版本
- http://59.110.124.211:9090/ 1.0版本
- http://yunpan.aoti.xyz:81/ 1.4.2版本
- https://pan.jwls.live/ 1.4.3版本
- https://bd.pkqjsq.top/ 1.4.3版本
- http://pan.0ddt.com/ 1.0版本
<!-- - https://129.146.174.245/ 1.4.5版本 -->
<!-- - http://pan.wbeu.cn/ 1.4.5版本 -->
<!-- - https://pan.lie01.com/ 1.4.3版本 -->
<!-- - https://www.bdwp.cf/ 1.4.3版本 -->
<!-- - http://39.105.69.60:82/ 1.0版本 -->

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

# 🔧Setting
首先Clone项目或进入[Releases](https://github.com/yuantuo666/baiduwp-php/releases)下载项目文件。<br />
然后访问 `install.php` 文件并填写相关信息。<br />
如果使用数据库，则需要先点击 `检查数据库连接` 连接数据库，保证账号密码正确。<br />
最后点击提交即可。

## 💻Demo
[暂不开放](http://imwcr.cn/api/bdwp/)<br />
因站长学习紧张加上精力有限，演示站没有时间维护，故暂时关闭。

## 📦New Changes
- 当前版本：`2.1.0`
- 更新日期：2021-02-17
<!-- 同志们，写更新日志要细致啊，不要写笼统的！ -->
- 修改内容：
  - 💥新增功能
    - 安装程序 `install.php` 自动检测旧版本配置文件 `config.php` 是否存在，若存在自动导入旧版本配置
    - 增加选择是否取消下载次数提醒功能
    - ✨安装时支持保留数据库数据
    - ✨后台管理页面支持删除数据
    - ✨增加四种SVIP账号切换模式
    - 增加首页公告自定义功能
  - 💪安全增强
    - 安装程序 `install.php` 自动检测是否安装过，如果安装则需进入管理员页面登录
  - ⚠错误修复
    - 修复部分页面检查密码功能失效问题
    - 修复首页小圆点无颜色错误
    - 修复不支持色彩模式的浏览器无法显示 `Sweetalert2` 弹窗问题
    - 修复解析数据一直为 `2.00GB` 问题
    - 修复管理员密码错误不提示
  - ♻代码优化
    - ✨将 `settings.php` 内部分请求方式改为 `ajax` ，增加加载提示框 <!-- 搞了四个小时，累死 -->
    - 优化提示文本（语法、严谨程度等），给一些提示框增加图标
    - 增加部分配置异常的处理程序
    - 优化部分 PHP 和 JavaScript 代码

## 🔔Thanks
- [baiduwp JavaScript 版](https://github.com/TkzcM/baiduwp "GitHub 项目")
- [PanDownload 网站](https://pandownload.com/ "PanDownload 网站")
- [Bootstrap 深色模式](https://github.com/vinorodrigues/bootstrap-dark "bootstrap-dark 项目")
