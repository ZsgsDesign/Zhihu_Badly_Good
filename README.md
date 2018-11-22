# Zhihu_Badly_Good

Selected Badly Good Zhihu Replies for you

## Author

My name's John and here's my blog 
[张佑杰的个人博客](https://www.johnzhang.xyz/)

## General Ideas

Badly Good Reply means "神回复" in Chinese, it's a metaphor, means you replied greatly and explicitly or just use one word, godliked.

This little PHP project provides you with a tool that could select Zhihu's badly good reply based on the following characteristics:

+ They are normally high voted
+ They are usually brief and explicit

So it's easy for us to locate most of the badly good replies with the following SQL query:
```
select * from answer where voteup_count>=1000 and length(content)<=50;
```
In which voteup_count means total votes one reply have.

## Detailed Process

First of all, we got to get all replies and their attrs. Luckily, with Chrome Dev Tools it's easy to locate the exact API.

Then it comes to the problem that PHP has max proc time. You definity could higher the max value like this but I would not recommend that : 

```
ini_set("max_execution_time", "600"); // higher to 10 min
```

In general we need tones of time to complete even just one category. So we use Ajax and build a frontend to call backend functions. That's the basic idea.

Then, after we finished the mining procedure. All we got is the SQL query mentioned eariler and, a nice frontend with random picked Badly Good Replies like this :

![Index Page](https://static.1cf.co/img/zhihu/1.png)

Well, of course I am not single but the point is, likewise, you can also give nice appearance to admin page. Just like I said, this project is for fun. So, fork it and do whatever you like.

## Installation

`zhihu.sql` contains all the answers I get,this is great for those who are lazy to get the data.

If you want to have a fresh start, try build a new database from sketch:
```
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for answer
-- ----------------------------
DROP TABLE IF EXISTS `answer`;
CREATE TABLE `answer`  (
  `aid` int(255) NOT NULL AUTO_INCREMENT,
  `id` int(11) NULL DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `question` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `voteup_count` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`aid`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2773 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for saved_topics
-- ----------------------------
DROP TABLE IF EXISTS `saved_topics`;
CREATE TABLE `saved_topics`  (
  `stid` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NULL DEFAULT NULL,
  `page_no` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`stid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3958 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

SET FOREIGN_KEY_CHECKS = 1;
```

`core/conn.php` provides `$db` variable and PDO connected database, if you want to change database connection, modify this file.

`core/config.php` provides `$topic_ids` variable which indicates whether a category can be processed.

## Other info

`proc.php` contains the primitive version of admin.php, check that for more inspiration. Also, think about the advantages and disadvantages proc.php have.