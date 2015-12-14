<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Loader
 *
 * @author Miro Hristov
 */
namespace quiz\models;
final class Loader {
    
    private static $namespaces=array();
    
    private function __construct(){}

    public static function registerAutoLoad(){
        spl_autoload_register(array("\quiz\models\Loader", "autoload"));
    }
    
    public static function autoload($class){       
self::loadClass($class);
    }
    
    //Takes the class which has to be included as parameter, attaches namespace,
    //and includes it the full path (namespace).
    public static function loadClass($class){
        foreach(self::$namespaces as $k=>$v) {
          
            if(strpos($class, $k)===0) {
                //echo $k."<br />".$v."<br />".$class;
            $file=  str_replace("\\",DIRECTORY_SEPARATOR,$class);
            $file = substr_replace($file, $v, 0, strlen($k)).".php";
            $file = realpath($file);
            if($file && is_readable($file)) {
                include $file;
            } else {echo "greda";}
            //echo "<br />".$file;
            }
        }
    }
    
    // registers all namespaces and puts it in array $namespaces
    public static function registerNamespace($namespace, $path){
        
        $namespace=trim($namespace);
        if(strlen($namespace)>0) {
            
            if(!$path) {
                throw new \Exception('Invalid path');
            }
            $_path=realpath($path);
            
            if($_path && is_dir($_path) && is_readable($_path)){
                
                self::$namespaces[$namespace."\\"]=$_path . DIRECTORY_SEPARATOR;
                }
                else {
                    throw new \Exception('Namespace directory read error: '.$_path);
                }
            
        }
        else {throw new \Exception('Invalid namespace'.$namespace);}
    }
    
    // TO DO - da se iztrie sled kato se pusne
    public function vard(){
        echo "<pre>".print_r(self::$namespaces, true)."</pre>";
    }
    }
