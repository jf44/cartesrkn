<?php

$json= '{"outputfile":"VGv2020_RKN_jf44-RKN-20201218_1140.csv"}';

echo "$json\n";
$json2=json_decode($json);

print_r($json2);


?>