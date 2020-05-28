<?php 
if (!defined('APP')) {die();}

$host = '127.0.0.1';
$user = 'socjpeg';
$password = 'qweqweqwe';
$dbname = 'socjpg';

try {
  $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->exec("set names utf8");
}
catch(PDOException $e) {
    dump ($e->getMessage());
}