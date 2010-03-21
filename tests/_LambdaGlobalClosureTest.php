<?php
require_once dirname(__FILE__) . '/../Phunctional/Lambda.php';

$a = 'global var';
$lambda = Lambda::begin()? $a :Lambda::end();

echo $lambda->call(), "\n", $lambda->apply(array());
