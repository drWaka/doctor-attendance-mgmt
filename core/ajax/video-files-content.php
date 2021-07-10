<?php
require '../php/_autoload.php';
require '../model/_autoload.php';

use wapmorgan\MediaFile\FileAccessException;
use wapmorgan\MediaFile\ParsingException;
use wapmorgan\MediaFile\MediaFile;

// JSON Response
$response = array(
    "httpStatus" => 'success',
    "type" => '',
    "level" => '',
    "content" => ''
);

$videosDir = '../videos/_';

if (is_dir($videosDir)) {
    $response["content"] = array();
    foreach (array_diff(scandir($videosDir), array('.', '..')) as $file) {
        $response["content"][] = [$file];
    }
}

// Encode JSON Response
encode_json_file($response);