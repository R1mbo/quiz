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

session_start();
spl_autoload_register(function($class){
    require_once ($class . '.php');
    });
