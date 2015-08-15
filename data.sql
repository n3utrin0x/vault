create database sysmod;
use sysmod;
create table changes(
	id int primary key auto_increment,
	title varchar(50),
	service varchar(100),
	risk int, /* 0 = Low, 1 = Medium, 2 = High */
	incident int,
	start int,
	end int,
	description text,
	what text,
	who text,
	backout text,
	postmodification text,
	user varchar(10),
	status int default 0
);
create table votes(
	id int primary key auto_increment,
	changeid int,
	vote int, /* 0 = Neutral, 1 = Approve, 2 = Deny, 3 = Stamp, 4 = Finish */
	user varchar(10),
	time int,
	comment text
);
create table servers(
	id int primary key auto_increment,
	changeid int,
	server varchar(100)
);

CREATE USER test@localhost IDENTIFIED BY 'test';
GRANT ALL ON sysmod.* TO test@localhost;