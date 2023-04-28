
#!/bin/bash

sudo mysql -e "CREATE DATABASE $1;"
sudo mysql -e "CREATE USER '$2'@'localhost' IDENTIFIED BY '$3';"
sudo mysql -e "GRANT ALL PRIVILEGES ON $1.* TO '$2'@'localhost';"
sudo mysql -u$2 -p$3 $1 -e "use $1; CREATE TABLE files (id int PRIMARY KEY NOT NULL AUTO_INCREMENT, name varchar(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"