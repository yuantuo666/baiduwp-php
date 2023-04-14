DROP TABLE IF EXISTS <dbtable>;
DROP TABLE IF EXISTS <dbtable>_ip;
DROP TABLE IF EXISTS <dbtable>_svip;

CREATE TABLE `<dbtable>` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `userip` TEXT NOT NULL,
  `filename` TEXT NOT NULL,
  `size` TEXT NOT NULL,
  `md5` TEXT NOT NULL,
  `path` TEXT NOT NULL,
  `server_ctime` TEXT NOT NULL,
  `realLink` TEXT NOT NULL,
  `ptime` DATETIME NOT NULL,
  `paccount` INTEGER NOT NULL
);

CREATE TABLE `<dbtable>_ip` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `ip` TEXT NOT NULL,
  `remark` TEXT NOT NULL,
  `add_time` DATETIME NOT NULL,
  `type` INTEGER NOT NULL
);

CREATE TABLE `<dbtable>_svip` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL,
  `svip_bduss` TEXT NOT NULL,
  `svip_stoken` TEXT NOT NULL,
  `add_time` DATETIME NOT NULL,
  `state` INTEGER NOT NULL,
  `is_using` DATETIME NOT NULL
);
