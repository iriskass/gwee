<?
include "env.php";

$app = new App();

System::$view = $app->output();

System::compile();