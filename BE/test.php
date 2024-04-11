<?php
function modifyArray($arr) {
    $arr[0] = 'Modified';
    $arr[] = 'New Element';
}

$myArray = ['Original'];
modifyArray($myArray);

print_r($myArray);

?>