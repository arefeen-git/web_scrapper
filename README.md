# web_scrapper

[![nginx](https://img.shields.io/badge/nginx-latest-brightgreen.svg)](https://nginx.org/)
[![php](https://img.shields.io/badge/php-latest-blue.svg)](https://www.php.net/)
[![symfony](https://img.shields.io/badge/symfony-latest-red.svg)](https://symfony.com/)
[![mysql](https://img.shields.io/badge/mysql-latest-orange.svg)](https://www.mysql.com/)
[![redis](https://img.shields.io/badge/redis-latest-red.svg)](https://redis.io/)
[![rabbitmq](https://img.shields.io/badge/rabbitmq-latest-brightgreen.svg)](https://www.rabbitmq.com/)
[![docker](https://img.shields.io/badge/docker-latest-blue.svg)](https://www.docker.com/)

> A web scraping project for learning purposes. This project uses Symfony as the framework. Mysql as the database. Redis is used to cache, retrieved data.
> Here Rabbitmq is used to process async requests (Tasks that require too long to process, listed and executed as async with symfony messenger).

## Disclaimer: [web_scrapper]

This disclaimer outlines the terms and conditions governing the use of **[web_scrapper]**, hereinafter referred to as "the Software." By accessing and utilizing the Software, you agree to comply with the following terms:

### Non-Commercial Use

The Software is intended solely for non-commercial purposes. You may not use, distribute, or profit from the Software in any commercial manner, including but not limited to resale, licensing, or monetization.

### Ethical Use and Data Mining

The Software is not designed, intended, or authorized for unethical data mining practices. You are strictly prohibited from engaging in any activities that involve unauthorized data collection, privacy infringements, or any actions that violate ethical data usage principles.

### EU Data Policy Compliance

The Software is developed in accordance with the rules and regulations set forth by the European Union's data protection policies, including but not limited to the General Data Protection Regulation (GDPR). However, it is essential to note that compliance with these policies is not a substitute for legal advice. Users are responsible for ensuring their activities align with applicable laws and regulations.

### Educational Purpose

The primary purpose of the Software is for educational and learning purposes. It is meant to facilitate exploration, experimentation, and skill development in various fields. The Software is not intended to replace professional advice, tools, or solutions.

### No Warranties or Guarantees

The Software is provided "as is," without any warranties, express or implied. The developers of the Software disclaim any liability, whether in contract, tort, or otherwise, arising from the use of the Software. Users assume full responsibility and risk for their use of the Software.

### Limitation of Liability

Under no circumstances shall the developer of the Software be liable for any direct, indirect, incidental, special, consequential, or punitive damages that result from the use or inability to use the Software, even if they have been advised of the possibility of such damages.

### Modification and Redistribution

You are permitted to modify the Software for personal use, learning, and experimentation. However, modified versions of the Software may not be redistributed without the explicit consent of the developer.

### Acceptance of Terms

By accessing or using the Software, you acknowledge that you have read, understood, and agree to abide by the terms and conditions outlined in this disclaimer.

The developer of **[web_scrapper]** reserves the right to update or modify this disclaimer at any time without prior notice. It is your responsibility to review this disclaimer periodically and remain aware of any changes.

If you do not agree with any part of this disclaimer or its terms, please refrain from using the Software.

Last updated: [13-08-2023]

For inquiries or concerns regarding this disclaimer, please contact [samsull.arefeen@gmail.com].

**[web_scrapper]** Md. Samsull Arefeen


## Setup Guide :

Follow these steps to set up and run the project:

1. **Checkout the Project:**

2. **Check Available Ports:**

   Run these commands to check available ports on your system:
   
        sudo lsof -i :80
        sudo lsof -i :8080
        sudo lsof -i :9000
        sudo lsof -i :3306
        sudo lsof -i :6379
        sudo lsof -i :5672
        sudo lsof -i :15672

3. **Create .env File:**

Create a `.env` file with appropriate credentials, following the example in `.env.test`.

4. **Update Directory Names:**

Update the following files to match your project directory name:

- `docker-compose.yml` - lines 15 & 32
- `Dockerfile` - line 13
- `default.conf` - line 6

For instance, replace `/var/www/html/my_project_directory/public` with `/var/www/html/web_scrapper/public` if your working directory is `web_scrapper`.

5. **MySQL Volume:** 

In the `docker-compose.yml` file, review the `volumes` section of the `mysql-service`. You can create a directory named "docker-mysql-data" at location "/var/www/html" `/var/www/html/docker-mysql-data` for MySQL data storage or replace it with `/var/lib/mysql`. Note that replacing with `/var/lib/mysql` might impact data if you have already MySQL installed on your host machine.

6. **Build and Run:**

Please make sure Docker and dcoker-compose is up and runnign in your host machnine. Run this command in your project directory:

        docker-compose up --build -d


7. **Install Dependencies:**

Inside the PHP container, install Composer dependencies. First run to get inside the container -
        
        docker exec -it php-container bash

Then run this to install composer dependencies -
        
        composer install

8. **Database Setup:**

Create the database:

        php bin/console doctrine:database:create


Run the migration script:

        php bin/console doctrine:migrations:migrate


9. **Start the Project:**

Start the project:

        symfony serve


Access the project at [http://localhost:8080/company/new](http://localhost:8080/company/new ), if the default configuration is unchanged.

10. **Run the Consumer:**

 In a new terminal tab, run:

 ```
 docker exec -it php-container bash
 ```

 & after getting inside the terminal run -

 ```
 php bin/console messenger:consume async --memory-limit=128M
 ```
 The more consumer you open/run the more faster mutiple registration code will be scrapped and stored. 

11. **Web Scraping Steps:**

 To extract company information from [https://rekvizitai.vz.lt/](https://rekvizitai.vz.lt/), follow these steps:

    1. In the registration code input field enter registration codes such as 300651537, 300940673, 302943102, 303116155.
       You can put them one by one like 300651537 or many by comma separated value 300651537, 300940673, 302943102, 303116155, ...

    2. Click the "Scrap and store" button.

12. **Company List:**

 After hitting the "Scrap and store" button, monitor the messages in terminal where you ran the worker/consumer. 
 After scraping, view the list of companies. You can edit and delete (soft) entries as needed.

If you encounter any issues or need assistance, contact me at [samsull.arefeen@gmail.com].
