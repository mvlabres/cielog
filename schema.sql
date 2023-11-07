create database `cielog`;

use cielog;

CREATE TABLE `user` (
  `id` int(21) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL,
  `created_date` date NOT NULL,
  `modified_date` date DEFAULT NULL,
  `created_by` int(21) DEFAULT NULL,
  `modified_by` int(21) DEFAULT NULL
)

ALTER TABLE `user` ADD CONSTRAINT `fk_user_cb` FOREIGN KEY (created_by) REFERENCES user(id);
ALTER TABLE `user` ADD CONSTRAINT `fk_user_mb` FOREIGN KEY (modified_by) REFERENCES user(id);

CREATE TABLE `user_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_type` varchar(50) NOT NULL,
  `function_name` varchar(50) NOT NULL
)

create table client(
	id int(21) not null auto_increment primary key,
    name varchar(50) not null,
    created_date date not null,
    created_by int(21) not null,
    modified_date date default null,
    modified_by int(21) default null
);

ALTER TABLE `client` ADD CONSTRAINT `fk_client_cb` FOREIGN KEY (created_by) REFERENCES user(id);
ALTER TABLE `client` ADD CONSTRAINT `fk_client_mb` FOREIGN KEY (modified_by) REFERENCES user(id);

alter table user add column client_id int(21);

insert into user(name, username, password, type, created_date) values ('Marcos Vinicius Labres de Oliveira', 'marcos.labres', 'getnis2023', 'adm', '2023-11-07');

