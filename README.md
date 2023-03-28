# 430Blue
A project to establish a simulation of a secure banking server.

## Setup
To setup the project, swap in an experimental node, and then run ctf_setup.sh

### Setting up the topology
- Go to isi.deterlab.net, sign in, and click on "begin an experiment."
- Give it a name and description.
- For the NS file, use `/proj/USC430/CTF_Bank/cctf.ns` in the `On Server` section.
- You can adjust swapping times however you want (make them shorter if you only plan to use the server briefly).
- Enable `Swap In Immediately`, and hit Submit.
- ssh into your user node: `ssh usc430[id]@users.isi.deterlab.net`
- On your user node, clone this repository: `git clone https://github.com/nshamilian/430Blue`. If you already have it clone, pull the latest version with `git pull`. Make sure you do this now, as the experimental node will not have access to the global Internet.
- Once your experimental node has finished swapping in, ssh into it with `ssh blue.[your-experiment-name].USC430`

Note that experimental nodes are shared, so if someone else has already begun an experiment, you can skip the first steps and ssh into their node.

### Running the setup script
- You may need to make the setup script, `ctf_setup.sh`, executable. Use `chmod +x ctf_setup.sh` to do so.
- Next, run it with `./ctf_setup.sh`.
- The setup script will first install required packages and libraries.
- After that, it will copy all files in the `html` folder to `/var/www/html/`, the default location from which the Apache will search for scripts.
- Then it will ask for a password. This will be the password used for the webserver to connect to the database server. For now, enter "temppassword" as that is the password used in the scripts.
- Afterwards, it will create a user `server@localhost` for the webserver to use, run the script `430sqlscript.sql`, which sets up the `bank` schema, and grant `server@localhost` access rights to `bank`.
- Finally, it will run the mydb secure installion process. You can say no to the password strength checker, but yes to anything that removes privileges or testing features.

### Updating an existing server
- Some changes, such as to libraries, may require reloading the server with `sudo systemctl reload apache2`. Simple changes like modifying a script do not, and only require changing the file in `/var/www/html`.
- If you need to update the schema, run `sudo mysql` to connect to the mysql server, `DROP DATABASE bank`, and then `quit`. Then run the SQL setup script again with `sudo mysql < 430sqlscript.sql`.

If you find any mistakes or unclear items in this README, please update it or contact Alex Prieger to get it resolved.