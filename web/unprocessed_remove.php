<?php
require_once 'functions.php';

$sms = new SMS();
echo json_encode($sms->unprocessed_remove())."\n";