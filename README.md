# web_scrapper

1. Checkout the project.

2. Check available ports (I am on ubuntu 22.04, please modify the command according to your os) - 

        sudo lsof -i :80
        
        sudo lsof -i :8080
        
        sudo lsof -i :9000
        
        sudo lsof -i :3306
        
        sudo lsof -i :6379
        
        sudo lsof -i :5672
        
        sudo lsof -i :15672

3. Please create a .env file with appropriate credentials, follow .env.test.

4. Please replace **my_project_directory** with the directory name you cloned this project to in below files -

   <code>docker-compose.yml - line 15 & 32
   Dockerfile - line 13
   default.conf - line 6</code>

   For example, if the line is somwthing like this - **/var/www/html/my_project_directory/public** change that to this - 
   **/var/www/html/web_scrapper/public** where web_scrapper is the current working directory.

5. Please check the volumes section of the mysql-service in docker-compose.yml file. You can create **/var/www/html/docker-mysql-data**
   directory for storing your mysql data or can replace it with **/var/lib/mysql**. But remeber, if you have mysql installed on your host machine,
   replacing **/var/www/html/docker-mysql-data** with **/var/lib/mysql** might corrupt you data.

6. Switch to project directory and run -

        docker-compose up --build -d

7. After successful build, run -

        docker exec -it php-container bash

8. Inside the php-container of docker, install all composer dependencies. So run -

        composer install

9. Now you need to run the migration script -

        php bin/console doctrine:migrations:migrate

10. Now run below command to start the project -

        symfony serve
   
   The project should be accessible from <a href="http://localhost:8080/company/new" target="_blank">http://localhost:8080/company/new</a>,
   If you haven't changed **default.conf** under nginx folder.

11. Open a new terminal tab with same directory and get inside the php container with **docker exec -it php-container bash** and then run -

        php bin/console messenger:consume async --memory-limit=128M

    The more step 9 you repeat (The more consumer you create), your multiple company scrapping will get inserted faster


12. Go to <a href="https://rekvizitai.vz.lt/en/company-search/" target="_blank">https://rekvizitai.vz.lt/en/company-search/</a>,
    complete the cloudflare captcha and then select any category and hit the search bar.


13. From the result page open the browser inspector tool and go to the Network -> Doc tab. 
    Reload the page, click the 1/ or 2/ (pagenumber) url that's being loaded. 
    Browse a bit below (Header section, and below you can find Request Headers) to copy Cookie value (cookie consent).
    The value will look like this -

    
    <code>CookieScriptConsent=%7B%22googleconsentmap%22%3A%7B%22ad_storage%22%3A%22targeting%22%2C%22analytics_storage%22%3A%22performance%22%2C%22functionality_storage%22%3A%22functionality%22%2C%22personalization_storage%22%3A%22functionality%22%2C%22security_storage%22%3A%22functionality%22%7D%2C%22action%22%3A%22accept%22%2C%22categories%22%3A%22%5B%5C%22unclassified%5C%22%2C%5C%22targeting%5C%22%5D%22%2C%22key%22%3A%223e8df365-6f2c-4c1a-80ad-d5c902d78b97%22%7D; VzLtLoginHash=RSU55dhwSq7ZcV3f9w; PHPSESSID=fa77hp3a3m9510ge9v15p256mn; _gid=GA1.2.1146864184.1691324377; _gat_UA-724652-3=1; cf_clearance=X9zki5ZFbDCFoK4FdT1q1gkl2odGdYGgx69Pg4kjAAs-1691336228-0-1-dd86ce74.8b164603.8a879194-250.2.1691336228; _ga_D931ERQW91=GS1.1.1691332400.86.1.1691336228.0.0.0; _ga=GA1.1.1096950485.1688928483</code>


14. Paste the value in Cookie-consent section of the scrapper <a href="http://localhost:8080/company/new" target="_blank">http://localhost:8080/company/new</a>.

15. Select any company from the search page and copy the registration field and paste it in the registration code filed.
    You can submit mutiple registration code as comma seperated values.

17. Hit the Scrap and store button.

18. Click the Show List button to view the companies. 
