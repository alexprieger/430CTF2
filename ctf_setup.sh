yes | sudo apt install apache2
yes | sudo apt install php libapache2-mod-php php-mysql
yes | sudo apt install mysql-server
sudo cp html/* /var/www/html/
# Create our user 'server'
sudo mysql -e "CREATE USER IF NOT EXISTS 'server'@'localhost' IDENTIFIED BY 'pbN967bgWUAgdb5X3BmBxI2F';"
# Create an examle database and populate it with some initial values
sudo mysql < sql-script.sql
sudo mysql -e "GRANT ALL PRIVILEGES ON mydb.* TO 'server'@'localhost';"
echo "Finished setting up."
