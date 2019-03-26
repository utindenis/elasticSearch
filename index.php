<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
<meta charset="UTF-8">
<h1>Elastic Search</h1>
<form method="post">
    <h3>Search:</h3>
    <p>Search in some document lines: <input type="text" name="searchAll"/></p>
    <h3>Cost:</h3>
    <p>From: <input type="text" name="from"/></p>
    <p>To: <input type="text" name="to"/></p>
    <h3>Search by fields:</h3>
    <p>Type bulding: <input type="text" name="typeBulding"/></p>
    <p>Rooms: <input type="number" name="rooms"/></p>
    <p>Accomodation format: <input type="text" name="accomodationFormat"/></p>
    <p><input type="submit" value="Search"/></p>
</form>
</body>
</html>

<?php

require __DIR__ . '/vendor/autoload.php';

try {
    $el = new \Elastic\ElSearch();
    $searchStruct = [];
    $searchAll = [];
    $from = [];
    $to = [];

    if (!empty($_POST['typeBulding'])) {
        $searchStruct['typeBulding'] = htmlspecialchars($_POST['typeBulding']);
    }
    if (!empty($_POST['accomodationFormat'])) {
        $searchStruct['accomodationFormat'] = htmlspecialchars($_POST['accomodationFormat']);
    }
    if (!empty($_POST['rooms']) && is_numeric($_POST['rooms'])) {
        $searchStruct['rooms'] = $_POST['rooms'];
    }
    if (!empty($_POST['searchAll'])) {
        $searchAll = htmlspecialchars($_POST['searchAll']);
    }
    if (!empty($_POST['from'])) {
        $from = htmlspecialchars($_POST['from']);
    }
    if (!empty($_POST['to'])) {
        $to = htmlspecialchars($_POST['to']);
    }

    if (!empty($searchAll) || !empty($from) || !empty($to) || !empty($searchStruct)) {
        echo '<h4>Result search:</h4>';
        $result = $el->searchMain($searchStruct, $searchAll, $from, $to);
        echo '<pre>', print_r($result['hits']['hits'], true), '</pre>';
    } else {
        echo '<h4>All documents:</h4>';
        $result = $el->getAllDocuments();
        echo '<pre>', print_r($result['hits']['hits'], true), '</pre>';
    }

} catch (Throwable $e) {
    var_dump($e);
}