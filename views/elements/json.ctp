<?php
Configure::write('debug', 0);
header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
header('Content-Type: application/json');
header("X-JSON: ".$javascript->object($result));
echo $javascript->object($result);
?>