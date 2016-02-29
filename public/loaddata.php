<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 2/23/16
 * Time: 7:28 PM
 */

require $_SERVER['DOCUMENT_ROOT'] . "/../vendor/autoload.php";

$db = new MysqliDb('localhost', 'a', 'a', 'tweetforbernie');

$bystateData = array (
    array("state" => "SC", "event"=>"primary", "when"=>""),
    array("state" => "SC", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
/*
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
    array("state" => "", "event"=>"", "when"=>""),
*/
);