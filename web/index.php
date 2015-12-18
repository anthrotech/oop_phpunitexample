<?php
require_once 'functions.php';

$sms = new SMS();
$token = $sms->get_auth_token();
$sms->save($_REQUEST['msisdn'],$_REQUEST['operatorid'],$_REQUEST['shortcodeid'],$_REQUEST['text'],null,$token);