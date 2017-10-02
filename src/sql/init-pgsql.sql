create table installed_migrations (
  id SERIAL,
  installation_time timestamp DEFAULT current_timestamp,
  migration_file_name varchar(255) NOT NULL,
  migration_file_checksum varchar(32) NOT NULL,
  success boolean,
  PRIMARY KEY (id)
)