<?php 
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Serializers\RBX;
include __DIR__.'/vendor/autoload.php';

$features = [
    'sepal-length',
    'sepal-width',
    'petal-length',
    'petal-width',
];

$inputs = [];

foreach ($features as $index => $feature) {
    $inputs[$index] = readline("{$feature}: ");
}

$estimator = PersistentModel::load(new Filesystem('knn.model'), new RBX());
$prediction = $estimator->predict(new Unlabeled([$inputs]))[0];

echo "Prediction: {$prediction}\n";
