<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 2/28/16
 * Time: 11:25 PM
 */
session_start();
session_unset();
$baseURL = "http://".$_SERVER['HTTP_HOST']."/";
header('Location:'.$baseURL);