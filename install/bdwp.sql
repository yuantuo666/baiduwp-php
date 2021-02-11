DROP TABLE IF EXISTS `<dbtable>`;
CREATE TABLE `<dbtable>` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userip` text NOT NULL COMMENT '用户ip',
  `filename` text NOT NULL COMMENT '文件名',
  `size` text NOT NULL COMMENT '文件大小',
  `md5` text NOT NULL COMMENT '文件效验码',
  `path` text NOT NULL COMMENT '文件路径',
  `server_ctime` text NOT NULL COMMENT '文件创建时间',
  `realLink` text NOT NULL COMMENT '文件下载地址',
  `ptime` datetime NOT NULL COMMENT '解析时间',
  `paccount` int(11) NOT NULL COMMENT '解析账号id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `<dbtable>_ip`;
CREATE TABLE `<dbtable>_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` text NOT NULL COMMENT 'ip地址',
  `remark` text NOT NULL COMMENT '备注',
  `add_time` datetime NOT NULL COMMENT '白黑名单添加时间',
  `type` tinyint(4) NOT NULL COMMENT '状态(0:允许,-1:禁止)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `<dbtable>_svip`;
CREATE TABLE `<dbtable>_svip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT '账号名称',
  `svip_bduss` text NOT NULL COMMENT '会员bduss',
  `svip_stoken` text NOT NULL COMMENT '会员stoken',
  `add_time` datetime NOT NULL COMMENT '会员账号加入时间',
  `state` tinyint(4) NOT NULL COMMENT '会员状态(0:正常,-1:限速)',
  `is_using` datetime NOT NULL COMMENT '是否正在使用(非零表示真)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;