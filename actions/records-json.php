<?php

require_once __DIR__ . '../fetch.php';

header('Content-Type: application/json');

echo json_encode($records);


?>