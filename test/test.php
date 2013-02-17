<?php
ini_set('max_execution_time', 300);
$result = 0;
for ($i = 0; $i < 1000000000; $i++) {
    $result=$result+($i * $i);
}
echo $result;
?>