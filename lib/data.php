<?php

date_default_timezone_set("Asia/Jakarta");

function loadData($filename) {
    $file = __DIR__ . '/../data/' . $filename;
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }
    $json = file_get_contents($file);
    return json_decode($json, true);
}

function saveData($filename, $data) {
    $file = __DIR__ . '/../data/' . $filename;
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}
