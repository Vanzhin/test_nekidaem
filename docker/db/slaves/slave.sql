CHANGE MASTER TO MASTER_HOST='db-master', MASTER_USER='slave_read_user', MASTER_PASSWORD='xSc1jnBR6r8GW9gQgNvdKsVqGDqm5l';
RESET SLAVE;
START SLAVE;