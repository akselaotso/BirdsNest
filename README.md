# BirdsNest - Reaktor application pre-assignment

At the time of this commit the instructions on the project can be found here: https://assignments.reaktor.com/birdnest/. The program fetches an xml document containing drones' serial numbers and locations. If the location is too close (within 100m) from the "birds nest", the program fetches the pilot's information and adds it to public/drones.json. The information is then retained for 10 minutes from the latest violation after which it is deleted. For multiple violations only the closest distance from the nest is kept. The information in the xml document updates every two seconds. For readability reasons the data on the website is rounded to two decimals, for example the site might display a drone's distance as 53.21 meters. The finished product can at the time of writing be found live at http://13.48.116.105/. 

My initial approachwas a sort of rapid static site generation method. This would have enabled extremely fast initial loads, but was unecessarily cumbersome on both the server and the endpoint. I ended up going with a server side rendering option combined with javascript fetching an update via a php document every 2 seconds. The entire project is built primarily in PHP with javascript only for refreshing the data without refreshing the page. The data file is still necessary in order for the program to remember the violations for any duration of time.

When a user requests the page the server first executes the PHP on the page and then serves the page to the user. After this the JavaScript starts running updating the page on a two second interval.

## How to start the web app
The actual app is in the app folder. The Dockerfile contains details for a tested image, but really you need two things: apache2 and php (the project has been tested on php8.1). 

You need to configure your apache2 server to use the projects 'public' folder as the document root as well as make all files in 'data' (note: only 'data', the folder is not in 'public') writeable for all users with the following command:
```
sudo chmod 666 [route to file]
```

## Testing
Tests written for PHP Unit version 9.5.27. You will need PHPUnit, if you have composer installed executing the following in the project folder will install the necessary items. Composer requires the php modules mbstring and xml. The test runs the primary function of data_collection class and compares the output to a correct output.
```
php composer.phar update
```
You can run the tests with by executing the phpunit directory. From the root folder the command would be:
```
./vendor/bin/phpunit
```
