<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$constraints = DB::select("SELECT conname, pg_get_constraintdef(oid) as def FROM pg_constraint WHERE conrelid = 'public.users'::regclass");
echo "=== Constraints da tabela users ===\n";
foreach ($constraints as $c) echo "  [{$c->conname}] {$c->def}\n";
