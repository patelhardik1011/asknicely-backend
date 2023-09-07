# Employee CSV Import and Listing

## Overview
This project is regarding importing employee into DB using provided sample CSV and list all employees.

### Testing
Here we have some unit testing to test the application.
```
tests/AppTest.php
```

## Installation

**To run this project, docker needs to be installed on the machine.**

To run this project please follow below steps:

1. Clone the repo from GitHub using below command:
    ````
   git clone https://github.com/patelhardik1011/asknicely-backend.git
2. After cloning please go to the project directory
   ````
   cd inventory-system
3. Now run below docker commands to run docker containers
   ````
   docker-compose down && docker-compose build && docker-compose up -d
4. Check if docker containers are running or not using below command. This will show docker containers running on the machine.
   ````
   docker ps
5. Install composer using below command
   ````
   docker exec php-container composer install

6. To access the project you will need to type following URL:
   ````
   http://127.0.0.1:8000
   or
   http://YOUR_IP:8000

Here I have used sample CSV file to be stored in Storage folder and reading data from that CSV file.

### Run the test cases
To run the test case, open the terminal in the root directory and run the following steps:

   1. Go inside docker using docker exec command like below
   ````
      docker exec -it php-conmtainer bash
      
      vendor/bin/phpunit tests      
   ````

### Note

Here in the docker file you can find MySql containers from you can access mysql db in terminal or any mysql IDE.

Make sure you use container name to connect DB in IDE or your IP address