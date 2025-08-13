<?php
$framesDir = "Admin/Templates/imgs/";
$files = glob($framesDir . "*.svg");
$frameList = [];

foreach ($files as $file) {
    $frameList[] = basename($file); // just the file name
}

header('Content-Type: application/json');
echo json_encode($frameList);
