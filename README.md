# Single php file script to view redmine self time logs

# Purpose

This script is created with intention to visualize the data in numbers or be it in charts format using this script we will be able to track / monitor the redmine time logs seamlessly

# Prerequisites

We need to make sure the REST API setting is enabled through the admin section  
more details : https://www.redmine.org/projects/redmine/wiki/rest_api

# Setup

you will need to update these two lines in the php file that contains
* $domain = "YOUR_DOMAIN";
* $key = "YOUR_KEY";
Details on retriving the key : https://www.redmine.org/projects/redmine/wiki/rest_api


# Features

* Shows number of distinct tickets addressed.
* Shows data only for current month till the current date
* Shows the average time spent each day
* Shows the list of tickets for which the data was logged
* A neat accordian table is implemented to sort out logs according to each date. 

# TO-DO

* Code sanitization.
* Optimization.
* Add comments.
* Input field to pass the user id and retrive id specific data. - Done 
* if no access to all the user list default to current user ID. - Done
* select the month from the GUI to view the data.
* Add a graphical view using charts so the data can be visualized.

GUI Credits:
* joey : https://codepen.io/jopico
* https://codepen.io/jopico/pen/EGpim
