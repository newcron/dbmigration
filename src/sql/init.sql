create table installed_migrations (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  installation_time timestamp default CURRENT_TIMESTAMP,
  migration_file_name varchar(255) not null,
  migration_file_checksum varchar(32) not null,
  success enum("true", "false"),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8