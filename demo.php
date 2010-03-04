<?php

require('tuentiAPI.class.php');

$con = new tuentiAPI('email@domain.com', 'password');
$r = $con->request('getUserNotifications', array());
echo '<pre>'.print_r($r, true).'</pre>';
