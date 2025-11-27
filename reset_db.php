#!/usr/bin/env php
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Schema;

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$connection = Capsule::connection();

// Drop all tables
$tables = array_column($connection->select("SHOW TABLES"), 'Tables_in_iestp_library');
$connection->statement('SET FOREIGN_KEY_CHECKS=0');
foreach ($tables as $table) {
    $connection->statement("DROP TABLE IF EXISTS $table");
}
$connection->statement('SET FOREIGN_KEY_CHECKS=1');

echo "All tables dropped successfully\n";
