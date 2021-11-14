<?php

require("../../include/Config.php");


// Custom values we want to map. This could be pulled from a database
$My_Keys = array("VPN SERVER" => "1.2.3.4",
    "VPN PORT" => "1234");


// If a valid file template is not presented then the page will return a 404.
$Template = new Config($My_Keys);

// We can later pull in more values we want mapped before displaying the template
$More_Keys = array("SERVICE" => "My Custom Service Name");
$Template->mapKeys($More_Keys);


// Currently display() is not defined so instead the magic function __call() 
// is being used. This function returns the entire output of the template.
echo $Template->display();

?>
