<?php

include_once(__DIR__ . '/../vendor/autoload.php');

$dummy = new \edwrodrig\ncbi\taxonomy\builder\Downloader;
echo "Downloading...\n";
$dummy->download();
echo "Unziping...\n";
$dummy->unzip();
echo "Building...";
$builder = $dummy->getBuilder();

$builder->build();

echo $builder->getTarget();

