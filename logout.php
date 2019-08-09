<?php
/*
 * MASTER LOGIN SYSTEM
 * @author David Huaraca
 * May 2019
 */


include "inc/init.php";

$user->logout();

header("Location: $set->url");