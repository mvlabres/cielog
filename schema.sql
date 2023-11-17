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
);

ALTER TABLE `user` ADD CONSTRAINT `fk_user_cb` FOREIGN KEY (created_by) REFERENCES user(id);
ALTER TABLE `user` ADD CONSTRAINT `fk_user_mb` FOREIGN KEY (modified_by) REFERENCES user(id);

CREATE TABLE `user_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_type` varchar(50) NOT NULL,
  `function_name` varchar(50) NOT NULL
);

CREATE TABLE `client`(
	`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(50) not null,
    `created_date` date not null,
    `created_by` int(21) not null,
    `modified_date` date default null,
    `modified_by` int(21) default null
);

ALTER TABLE `client` ADD CONSTRAINT `fk_client_cb` FOREIGN KEY (created_by) REFERENCES user(id);
ALTER TABLE `client` ADD CONSTRAINT `fk_client_mb` FOREIGN KEY (modified_by) REFERENCES user(id);

alter table `user` add column client_id int(21);
ALTER TABLE `user` ADD CONSTRAINT `fk_user_ci` FOREIGN KEY (client_id) REFERENCES client(id);

insert into user(name, username, password, type, created_date) values ('Marcos Vinicius Labres de Oliveira', 'marcos.labres', 'getnis2023', 'adm', '2023-11-07');

insert into user_access(user_type, function_name) values ('adm', 'users');
insert into user_access(user_type, function_name) values ('adm', 'access');
insert into user_access(user_type, function_name) values ('adm', 'access_new');
insert into user_access(user_type, function_name) values ('adm', 'access_list');
insert into user_access(user_type, function_name) values ('adm', 'register');
insert into user_access(user_type, function_name) values ('adm', 'register_employee');
insert into user_access(user_type, function_name) values ('adm', 'register_client');
insert into user_access(user_type, function_name) values ('adm', 'register_shipping_company');
insert into user_access(user_type, function_name) values ('adm', 'register_vehicle_type');
insert into user_access(user_type, function_name) values ('adm', 'register_driver');
insert into user_access(user_type, function_name) values ('adm', 'delete_access');
insert into user_access(user_type, function_name) values ('adm', 'edit_access');

insert into user_access(user_type, function_name) values ('client', 'access');
insert into user_access(user_type, function_name) values ('client', 'access_list');

insert into user_access(user_type, function_name) values ('operator', 'access');
insert into user_access(user_type, function_name) values ('operator', 'access_new');
insert into user_access(user_type, function_name) values ('operator', 'access_list');
insert into user_access(user_type, function_name) values ('operator', 'register');
insert into user_access(user_type, function_name) values ('operator', 'register_employee');
insert into user_access(user_type, function_name) values ('operator', 'register_shipping_company');
insert into user_access(user_type, function_name) values ('operator', 'register_vehicle_type');
insert into user_access(user_type, function_name) values ('operator', 'register_driver');
insert into user_access(user_type, function_name) values ('operator', 'edit_access');

CREATE TABLE `employee` (
  `id` int(21) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `registration` varchar(50) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `business_id` int(21) NOT NULL,
  `vehicle` varchar(50) DEFAULT NULL,
  `vehicle_plate` varchar(10) DEFAULT NULL,
  `created_date` date NOT NULL,
  `modified_date` date DEFAULT NULL,
  `created_by` int(21) NOT NULL,
  `modified_by` int(21) DEFAULT NULL
);

ALTER TABLE `employee` ADD CONSTRAINT `fk_employee_cb` FOREIGN KEY (created_by) REFERENCES user(id);
ALTER TABLE `employee` ADD CONSTRAINT `fk_employee_mb` FOREIGN KEY (modified_by) REFERENCES user(id);

ALTER TABLE `employee` ADD CONSTRAINT `fk_employee_bi` FOREIGN KEY (business_id) REFERENCES client(id);

CREATE TABLE `shipping_company` (
  `id` int(21) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `created_date` date NOT NULL,
  `modified_date` date DEFAULT NULL,
  `created_by` int(21) NOT NULL,
  `modified_by` int(21) DEFAULT NULL
);

ALTER TABLE `shipping_company` ADD CONSTRAINT `fk_sc_cb` FOREIGN KEY (created_by) REFERENCES user(id);
ALTER TABLE `shipping_company` ADD CONSTRAINT `fk_sc_mb` FOREIGN KEY (modified_by) REFERENCES user(id);

CREATE TABLE `vehicle_type` (
  `id` int(21) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `created_date` date NOT NULL,
  `modified_date` date DEFAULT NULL,
  `created_by` int(21) NOT NULL,
  `modified_by` int(21) DEFAULT NULL
);
ALTER TABLE `vehicle_type` ADD CONSTRAINT `fk_vt_cb` FOREIGN KEY (created_by) REFERENCES user(id);
ALTER TABLE `vehicle_type` ADD CONSTRAINT `fk_vt_mb` FOREIGN KEY (modified_by) REFERENCES user(id);

CREATE TABLE `driver` (
  `id` int(21) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `cnh` varchar(20) DEFAULT NULL,
  `cnh_expiration` date DEFAULT NULL,
  `cpf` varchar(14) NOT NULL,
  `shipping_company` varchar(100) DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `vehicle_plate` varchar(10) DEFAULT NULL,
  `vehicle_plate2` varchar(10) DEFAULT NULL,
  `vehicle_plate3` varchar(10) DEFAULT NULL,
  `record_type` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `block_reason` varchar(200) DEFAULT NULL,
  `created_date` date NOT NULL,
  `modified_date` date DEFAULT NULL,
  `created_by` int(21) NOT NULL,
  `modified_by` int(21) DEFAULT NULL
);
ALTER TABLE `driver` ADD CONSTRAINT `fk_driver_cb` FOREIGN KEY (created_by) REFERENCES user(id);
ALTER TABLE `driver` ADD CONSTRAINT `fk_driver_mb` FOREIGN KEY (modified_by) REFERENCES user(id);

CREATE TABLE `driver_access` (
  `id` int(21) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `driver_id` int(21) NOT NULL,
  `business_id` int(21) NOT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `vehicle_plate` varchar(10) DEFAULT NULL,
  `vehicle_plate2` varchar(10) DEFAULT NULL,
  `vehicle_plate3` varchar(10) DEFAULT NULL,
  `inbound_invoice` varchar(20) DEFAULT NULL,
  `outbound_invoice` varchar(20) DEFAULT NULL,
  `operation_type` varchar(20) DEFAULT NULL,
  `user_outbound_id` int(21) DEFAULT NULL,
  `rotation` varchar(30) DEFAULT NULL,
  `created_date` date NOT NULL,
  `modified_date` date DEFAULT NULL,
  `created_by` int(21) NOT NULL,
  `modified_by` int(21) DEFAULT NULL
);
ALTER TABLE `driver_access` ADD CONSTRAINT `fk_da_cb` FOREIGN KEY (created_by) REFERENCES user(id);
ALTER TABLE `driver_access` ADD CONSTRAINT `fk_da_mb` FOREIGN KEY (modified_by) REFERENCES user(id);
ALTER TABLE `driver_access` ADD CONSTRAINT `fk_da_di` FOREIGN KEY (driver_id) REFERENCES driver(id);
ALTER TABLE `driver_access` ADD CONSTRAINT `fk_da_bi` FOREIGN KEY (business_id) REFERENCES client(id);
ALTER TABLE `driver_access` ADD CONSTRAINT `fk_da_uoi` FOREIGN KEY (user_outbound_id) REFERENCES user(id);

CREATE TABLE `employee_access` (
  `id` int(21) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `employee_id` int(21) NOT NULL,
  `vehicle` varchar(50) DEFAULT NULL,
  `vehicle_plate` varchar(10) DEFAULT NULL,
  `user_outbound_id` int(21) DEFAULT NULL,
  `rotation` varchar(30) DEFAULT NULL,
  `created_date` date NOT NULL,
  `modified_date` date DEFAULT NULL,
  `created_by` int(21) NOT NULL,
  `modified_by` int(21) DEFAULT NULL
);
ALTER TABLE `driver_access` ADD CONSTRAINT `fk_ea_cb` FOREIGN KEY (created_by) REFERENCES user(id);
ALTER TABLE `driver_access` ADD CONSTRAINT `fk_ea_mb` FOREIGN KEY (modified_by) REFERENCES user(id);
ALTER TABLE `driver_access` ADD CONSTRAINT `fk_ea_di` FOREIGN KEY (driver_id) REFERENCES driver(id);
ALTER TABLE `driver_access` ADD CONSTRAINT `fk_ea_uoi` FOREIGN KEY (user_outbound_id) REFERENCES user(id);