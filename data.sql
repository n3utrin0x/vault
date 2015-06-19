create database vault;
use vault;
create table notes(
	id int primary key auto_increment,
	title varchar(50),
	content text,
	label int
);
