# BirdsNest - Reaktor application pre-assignment

At the time of this commit the instructions on the project can be found here: https://assignments.reaktor.com/birdnest/. The program fetches an xml document containing drones' serial numbers and locations. If the location is too close (within 100m) from the "birds nest", the program fetches the pilot's information and adds it to public/drones.json. The information is then retained for 10 minutes from the latest violation after which it is deleted. For multiple violations only the closest distance from the nest is kept. The information in the xml document updates every two seconds. For readability reasons the data on the website is rounded to two decimals, for example the site might display a drone's distance as 53.21 meters.

My approach is a sort of rapid static site generation method. I felt this was an optimized solution and perhaps more interesting than using a SSR framework. The rendered HTML with the complete data is extremely fast to load. A separate database or "data file" / file containing the data had to be maintained in any case meaning a proram had to constantly run updating the file. The HTML rendering's effect on performance compared to the data management is rather marginal and hence a solid option. A data file was the simpler solution and quite sufficient for the scope of this project. In a more complex or larger project a proper database option might be preferrable.

For the rapid SSG I build a php daemon with a two second sleep cycle. The daemon first calls functions from Modules/data_collection/data_collection.php to collect and process the data, updating the drones.json document. After that the daemon calls a function from Templates/render.php which renders the html page with the updated data. This way an end user always recieves up to date data absolutely immediately on loading the page.

After the page has loaded a JavaScript function starts running, again with a two second interval, updating the data on the site without refreshing the page.

## How to start the web app
The actual app is in the app folder. The Dockerfile contains details for a tested image, but really you need two things: apache2 and php (the project has only been tested on php8.1). 

Specific to this project on the server side: 

You can start the php scrip to the background, for example with:
```
nohup php [project location]/data_collection_daemon.php &
```

And you need to configure your apache2 server to use the projects 'public' folder as the document root. 

## Testing
Tests written for PHP Unit version 9.5.27. You will need PHPUnit, if you have composer installed executing the following in the project folder will install the necessary items. Composer requires the php modules mbstring and xml. Testing is limited to locally operating functions in the data collection module.
```
php composer.phar update
```
You can run the tests with by executing the phpunit directory. From the root folder the command would be:
```
./vendor/bin/phpunit
```
