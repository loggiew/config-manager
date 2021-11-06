<?php
######
# Config builder
# Author: Logan Anderson
# (c) 2021
######


class Api {
    public $ini_config;
    public $filepath;
    public $filename;
    public $filetype;
    public $formats;

    function __construct() {
        $this->ini_config = parse_ini_file(".ini", true);
        $this->filepath = "templates";
        $this->filename = $this->ini_config["default"]["FILENAME"];
        $filepath = $this->filepath;
        $filename = $this->filename;
        $this->formats = array( "xml" => "text/xml",
            "txt" => "text/plain",
            "html" => "text/html",
            "js" =>   "text/javascript");
        if (!empty($_GET["filename"])) {
            $filename = $_GET["filename"];
            $filename = preg_replace( '/[^a-z0-9\.\/]+/', '-', strtolower( $_GET["filename"] ) );
            $filename = preg_replace( '/(\.\.|\.\/)/', '', strtolower( $filename ) );
        }
        $this->filename = "$filepath/$filename";
        $filename = $this->filename;
        $this->filetype = pathinfo($filename, PATHINFO_EXTENSION);
        $filetype = $this->filetype;
        if (!file_exists($filename)) {
            header("HTTP/1.0 404 Not Found");
        }
        if (array_key_exists($filetype, $this->formats)) {
            header ("Content-Type:" . $this->formats[$filetype]);
        }
        $this->template_config = file_get_contents($filename);
        if (!empty($_GET["id"])) {
            $ID = $_GET["id"];
            $config = $this->ini_config[$ID];
        }
        foreach ($this->ini_config["default"] as $option => $setting) {
            if (empty($config[$option])) {
                $config[$option] = $setting;
            }
        }
        if (!empty ($ID) && !empty($this->ini_config[$ID])) {
            foreach ($this->ini_config["default"] as $option => $setting) {
                if (!empty($this->ini_config[$ID][$option])) {
                    $config[$option] = $this->ini_config[$ID][$option];
                }
            }
        } else {
            $config = $this->ini_config["default"];
        }
        foreach ($_GET as $key => $value) {
            $key_upper = strtoupper($key);
            $config[$key_upper] = $value;
        }
        foreach ($config as $key => $value) {
            $replacement = "##$key##";
            $this->template_config = str_replace($replacement, $value, $this->template_config);
        }
        
    } 

    public function __call($name, $args) {
        #echo $this->template_config;
        return $this->template_config;
    }
   
}


?>
