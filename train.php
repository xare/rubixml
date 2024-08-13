<?php

use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\CrossValidation\Metrics\Accuracy;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Extractors\CSV;
use Rubix\ML\Kernels\Distance\Euclidean;
use Rubix\ML\Loggers\Screen;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Serializers\RBX;
use Rubix\ML\Transformers\NumericStringConverter;

include __DIR__.'/vendor/autoload.php';

try {
    $dataset = new CSV('dataset.csv', true);

    $training = (Labeled::fromIterator($dataset->getIterator()))->apply(new NumericStringConverter());
    // We take 30 rows in order to test accuracy.
    $testing = $training->randomize()->take(30);

    $estimator = new PersistentModel(
        new KNearestNeighbors(5, true, new Euclidean()), 
        new Filesystem('knn.model'), 
        new RBX()
    );

    $estimator->train($training);
    $estimator->save();
    
    $predictions = $estimator->predict($testing);

    $score = (new Accuracy())->score($predictions, $testing->labels());
    (new Screen())->info('Accuracy: '.$score);
    


} catch(Throwable $exception) {
    (new Screen())->error($exception->getMessage());
}
