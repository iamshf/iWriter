create database if not exists iWriter default charset utf8;
use iWriter;

drop table if exists post;
create table post(
    id int unsigned primary key auto_increment comment 'id', 
    title varchar(100) not null default '' comment '标题', 
    subtitle varchar(100) not null default '' comment '副标题', 
    foreword varchar(300) not null default '' comment '引言', 
    content text not null default '' comment '内容', 
    gmt_add timestamp not null default current_timestamp comment '添加时间',
    gmt_modify timestamp not null default 0 comment '修改时间',
    status tinyint unsigned not null default 1 comment '0-禁用；1-正常；2-草稿'
) engine = myisam default charset=utf8;

drop table if exists user;
create table user(
    id int unsigned primary key auto_increment comment 'id', 
    name varchar(20) not null comment '登录名',
    pwd varchar(255) not null comment '密码',
    gmt_add timestamp not null default current_timestamp comment '添加时间',
    unique key uk_name (name)
)engine = myisam default charset=utf8;

drop table if exists category;
create table category (
    id int unsigned primary key auto_increment comment 'id',
    name varchar(20) not null default '' comment 'category name',
    pid int unsigned not null default 0 comment '父节点id',
    lv int unsigned not null comment '左值',
    rv int unsigned not null comment '右值',
    deep int unsigned not null comment '层级深度',
    enabled tinyint unsigned not null default 1 comment '0-禁用；1-启用',
    remark varchar(100) not null default '' comment '备注'
)engine = myisam default charset=utf8;

drop table if exists rel_category_post;
create table rel_category_post (
    post_id int unsigned not null,
    category_id int unsigned not null,
    primary key (post_id,category_id)
);
