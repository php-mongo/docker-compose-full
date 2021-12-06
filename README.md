## About PhpMongoAdmin (PMDbA)

PhpMongoAdmin is a Web-based MongoDb management console, written in PHP and leveraging great tools like Laravel and Vue.
The familiar interface allows you to manage many aspects of your MongoDB installation:

PhpMongoAdmin source code is located here: [gtihub.com/php-mongo/admin](https://github.com/php-mongo/admin).  
Read more here: [PhpMongoAdmin ReadMe](PHPMONGOADMIN.MD)

## Docker-Compose-Full: Stand-Alone Build

This docker-compose build of PhpMongoAdmin is ann all-inclusive build environment with Apache2 & MongoDB.

### This is not a docker image build: check our [docker](https://github.com/php-mongo/docker) image repository for that one!

You could use this build if you wanted to try out PhpMongoAdmin outside your existing development environments or if you don't have a web server handy.  
This build includes a <b>MongoDB</b> (image & container) and allows you to test the application anywhere, including a Windows box with <b>Docker Desktop</b> installed.  
MongoDB's data will be persisted inside: /storage/mongodb/

## Requires
- Git or Git bash
- Recent version of Docker
    - tested successfully with version: 20.10.11 (docker-ce)
    - issue occurred using an earlier version (docker)
- Recent version of docker-compose
    - tested successfully with version: 1.29.2
    - issue occurred using an earlier version along with an older docker
- On Windows * installing Docker Desktop should provide the required environment

## Quick Start
Run this command from an empty directory to fetch the repository and begin the setup process:  
$ wget https://phpmongoadmin.com/install/docker-all.sh -O - | bash

## How it works
The application will be installed into the Host container at /usr/share/phpMongoAdmin  
An apache config will be copied to /etc/apache2/conf-available/phpMongoAdmin.conf and will be linked to /etc/apache2/conf-enabled/phpMongoAdmin.conf  
This configuration will make the application available at http://localhost/phpmongoadmin  
The URL http://localhost/phpMongoAdmin will redirect to http://localhost/phpmongoadmin  
The default web page (index.html) is linked as a volume from a directory within the application: /var/www/html  
You can modify the mapping of that volume in the /docker/docker-compose.yml on line: 34  
If you prefer to use a VirtualHost type of installation use these files, interchange the names:
- docker/docker-compose-vhost.yml >> docker/docker-compose.yml
- docker/build/Dockerfile_vhost >> docker/build/Dockerfile
- update the: ServerName value in this file:
    - docker/build/php-mongo-web/config/vhost_phpMongoAdmin.conf
- then run the setup process detailed below
- Note: using the VirtualHost setup will change the location of the application codebase to:
    - /var/hosting/phpmongoadmin

## Getting started

Follow these steps to get up and running with minimal fuss.
- Download or clone this repository to your target directory
- cd (change directory) to the root directory of the application
- For setup on Windows, right-click and select 'Git Bash Here' (Git for Windows required)
- List (ls-la) the directory contents to confirm that you can see:
    - a folder name 'docker', a file named 'pmasetup.sh'

#### At this point you should prepare the environment files
The application will install 'as-is' but its highly recommended that you at least update the default passwords.  
Follow these steps to set up the environment:
- Open the file docker/build/php-mongo-web/config.env.example with an editor such as Notepadd++
    - This file will be copied to the root directory of the application as .env by the setup script
    - The .env file required for Laravel and is populated with working default settings
    - Advanced users can use adjust these settings to suit their needs
    - !! Do not populate the APP_KEY - it will be auto generated in later steps !!
    - Read [the docs](https://phpmongoadmin.com/support/documentation) for detailed information on handling these settings
- Open the file: 'docker/docker.env' with an editor such as Notepadd++
- For enhanced security you should update the 'MONGO_USER' & 'MONGO_USER_PWD' values.
    - Make a note of these values: you will use them during the application setup for the 'Control User'
    - 'save & close' the configuration files

#### You can view the detected configuration using this command
- docker-composer config

#### Now you are ready to execute the setup commands

Type these commands at a prompt in the application root:

- source docker/pmasetup.sh
- On Windows:
    - pmasetup win-build
    - win-build uses 'winpty' as a command prefix (provide by Git for Windows)
- On Unix based:
    - pmasetup build
- The build process will complete these steps:
    - build the docker containers
    - copy configuration files into place
    - run 'composer install'
    - enters the container shell and runs the 'dosetup' command
    - starts the queue worker
- Once the build process has completed the last few line should indicate: 'Personal access client created successfully' along with a Client ID and secret.
- The prompt will no longer be available as the terminal is locked by the Laravel worker task.
- Pressing Ctrl + z should unlock the terminal, and will stop the listener, however there won't be any notifications sent or logged.
- You can use MailHog to receive emails or monitor the email logs: /storage/logs/email.log

####If the build process was unable to access the host shell, the following steps can complete the setup process manually
-To access the Host container shell:
- On Windows:
    - winpty docker exec -it docker_php-mongo-web_1 bash
- On Unix based:
    - docker exec -it docker_php-mongo-web_1 /bin/bash
- You should now have a cli shell active on the container, run the following commands.
- ! If the above commands fail to open the container`s BASH, you can try to gain access via Docker Desktop or try a cup of tea !
- Once you have switched to the container shell, run the following single command:
    - dosetup
    - ! This command should run a sequence of commands and display the results to the terminal !
    - ! If this command cannot run or is not found, run the sequence of commands below !
- ! Do NOT run these again if the 'dosetup' command was successful !
- Make sure the current path is: /usr/share/phpMongoAdmin
- This generates the system key:
    - php artisan key:generate --ansi
- This runs the default migrations:
    - php artisan migrate
- Installs the Passport encryption keys
    - php artisan passport:install
- Deploys passport - generates key required to generate tokens
    - php artisan passport:keys
- Creates the passport 'personal key'
    - php artisan passport:client --personal
- Starts the queue schedule worker
- php artisan queue:work

####!! If you were unable to open the container shell earlier, but have now accessed it another way, you may not have the 'dosetup' command in your PATH
- You can run the command like this:
- !! There is no need to do this if the keys and oauth client have already been created !!
    - First ensure you are in the application root:
        - pwd ( should display /usr/share/phpMongoAdmin)
        - try: dosetup
        - if you get something like: /bin/sh: 21: dosetup: not found
        - try this:
        - /bin/bash -c "source /etc/profile.d/phpmongoadmin.sh && dosetup"
        - This will run the same sequence of commands

#### You can now open a browser and load the 'localhost'

Opening http://localhost with no path should load the default Html page.
Opening the browser with http://localhost/phpmongoadmin should initialise the PhpMongoAdmin setup
Read the [setup guide](https://phpmongoadmin.com/support/documentation/setup) in our docs for further guidance

### Things to be aware of and known issues

1) If your on Linux and 'localhost' does not load, check your SELinux settings
2) If you have previously created the 'Control User' and logged in to the application, then at some later time you decide to rebuild docker from scratch you will need to clear the 'user`s token' from 'Local Storage' in your browser.
3) If you deleted the .env file and ran the build process again a new application key will be generated; At this closure any previously created users will NOT be able to access the MongoDb server due to their encrypted password no longer being readable.  
   a) The control-user password can be reset: follow the link from the login modal.  
   b) If your application cannot deliver emails, you can access the reset email from the email log. All emailing is logged by default: this setting 'MAIL_LOG_CHANNEL=emails' can be disabled in the .env file.  
   c) Alternatively, delete the database/sqlite/database.sqlite file and run a new setup process: all existing login users and server configurations will be removed
4) If the application loads, and you see an error message relating to database connection or no database, or an error message that contains: "Failed to parse MongoDB URI: 'mongodb+srv'" then it's likely that the .env file setting: IS_DOCKER_AP= might be set to false. Make sure this setting is true for docker builds
5) If this repository in cloned to a Window box, when you run 'pmasetup win-build' and the last three lines shown after the build completes have error message like:  
   a) /etc/profile.d/phpmongoadmin.sh: line 3: syntax error near unexpected token `$'{\r'  
   b) this is due to Window rewriting the line endings - to fix do the following  
   c) Using Notepadd++ open the local version of the file: docker/build/php-mongo-web/scripts/phpmongoadmin.sh  
   d) Click > Edit > EOL Conversion > then select > Unix (LF) and save the file  
   e) Run: 'pmasetup win-build' again - the 3 error message lines should not appear after build completion   
