<?php
require '../php/_autoload.php';
require '../model/_autoload.php';

$waka = QuestionDtl::getByPage(array(
    "questionMstrId" => '1',
    "questionGrpId" => '1',
    "sorting" => '1'
));

echo var_dump($waka);