yes | sudo apt install apache2
yes | sudo apt install php libapache2-mod-php php-mysql
yes | sudo apt install mysql-server
sudo cp html/* /var/www/html/
while :
do
	read -rep 'Enter a password:' -s pswd
	echo
	read -rep "Enter the same password again:" -s pswd2
	echo
	if [ "$pswd" == "$pswd2" ]
	then
		break
	else
		echo "Passwords do not match."
	fi
done
# Create our user 'server'
sudo mysql -e "CREATE USER 'server'@'localhost' IDENTIFIED BY '$pswd';"
# Create an examle database and populate it with some initial values
sudo mysql < 430sqlscript.sql
sudo mysql -e "GRANT ALL PRIVILEGES ON bank.* TO 'server'@'localhost';"
sudo mysql_secure_installation
echo "Finished setting up."
