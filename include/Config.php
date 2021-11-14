<?php
######
# Config builder
# Author: Logan Anderson
# (c) 2021
######


class Config {
    public $ini_config;
    public $device_config;
    public $id;
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
        $config = $this->device_config;
        $this->formats = array( "xml" => "text/xml",
            "txt"  => "text/plain",
            "conf" => "text/plain",
            "cnf"  => "text/plain",
            "html" => "text/html",
            "js"   => "text/javascript");
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
            $this->id = $_GET["id"];
            $config = $this->ini_config[$this->id];
        }

        $config = $this->mapDefaults($config);
        $config = $this->mapConfig($config);
        $config = $this->mapBrowser($config);
        $this->device_config = $config;
    } 

    public function __call($name, $args) {
        $config = $this->device_config;
        $template = $this->template_config;
        $template = $this->parseTemplate($template, $config);
        return $template;
    }
  
    public function parseTemplate($template, $config) {
        foreach ($config as $key => $value) {
            $replacement = "##$key##";
            $template = str_replace($replacement, $value, $template);
        }
        return $template;
    }

    public function mapDefaults($config) {
        foreach ($this->ini_config["default"] as $option => $setting) {
            if (empty($config[$option])) {
                $config[$option] = $setting;
            }
        }
        return $config;
    }

    public function mapConfig($config) {
        if (!empty ($this->id) && !empty($this->ini_config[$this->id])) {
            foreach ($this->ini_config["default"] as $option => $setting) {
                if (!empty($this->ini_config[$this->id][$option])) {
                    $config[$option] = $this->ini_config[$this->id][$option];
                }
            }
        } else {
            $config = $this->ini_config["default"];
        }
        return $config;
    }

    public function mapBrowser($config) {
        foreach ($_GET as $key => $value) {
            $key_upper = strtoupper($key);
            $config[$key_upper] = $value;
        }
        return $config;
    }
}


?>
