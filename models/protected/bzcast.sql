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
INSERT INTO `category` (id,condition,feed,title) VALUES (1,'{"category":"Cate1"}','feed_cate1','Title_Cate1');
INSERT INTO `category` (id,condition,feed,title) VALUES (2,'["like","category","Cate2"]','feed_cate2','Title_Cate2');

