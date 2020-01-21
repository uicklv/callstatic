<?php
require_once '../vendor/autoload.php';

use App\User;


//User::findById(2);
//User::findByEmailAndByStatus('admin@gmail.com', 'admin');
//User::findBetweenCreatedAt('2020-01-01', '2020-01-05');
User::findBetweenCreatedAtAndByStatus('2020-01-01', '2020-01-05', 'client');
//$status = array('client', 'admin');
//User::findBetweenCreatedAtAndInStatus('2020-01-01', '2020-01-03', $status);








