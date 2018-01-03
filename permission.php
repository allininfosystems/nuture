<?php
$output = shell_exec('sudo chmod -R 777 var');
echo "<pre>$output</pre>";
$output = shell_exec('sudo chmod -R 777 pub');
echo "<pre>$output</pre>";
?>