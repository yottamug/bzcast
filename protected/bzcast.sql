BEGIN TRANSACTION;
CREATE TABLE "mp3file" (
	`id`	INTEGER,
	`title`	TEXT,
	`filename`	TEXT UNIQUE,
	`category`	TEXT,
	`author`	TEXT,
	`pubDate`	TEXT,
	`duration`	TEXT,
	`size`	NUMERIC,
	`bitrate`	INTEGER,
	`frequency`	INTEGER,
	PRIMARY KEY(id)
);
CREATE TABLE "category" (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`condition`	TEXT UNIQUE,
	`feed`	TEXT UNIQUE,
	`title`	TEXT
);
INSERT INTO `category` (id,condition,feed,title) VALUES (1,'{"category":"早霸王 (2016)"}','gmk_2016','GMK 2016');
INSERT INTO `category` (id,condition,feed,title) VALUES (5,'["like","category","公子會"]','ptm','PYM');
INSERT INTO `category` (id,condition,feed,title) VALUES (6,'["like","filename",["早霸王","梁嘉琪"]]','gmk_special2013','GMK Special 2013');
COMMIT;
