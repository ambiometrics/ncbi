<?php

include_once(__DIR__ . '/../vendor/autoload.php');

$dummy = new \edwrodrig\ncbi\TaxonomyDownloader;
//$dummy->download();
$dummy->unzip();
foreach ( $dummy->readNames() as $id => $name ) {
    echo $id , " " , $name , "\n";
}
