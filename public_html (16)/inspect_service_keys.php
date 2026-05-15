<?php
$json = file_get_contents('http://localhost/Zenno-main/api.php?action=get_service_detail&id=1');
$data = json_decode($json, true);
if (!$data) { echo "Failed to decode JSON:\n"; var_dump($json); exit; }
print_r(array_keys($data['service']));
?>