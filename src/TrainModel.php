<?php

require '../vendor/autoload.php';

use App\Users;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Transformers\OneHotEncoder;
use Rubix\ML\Persisters\ModelManager;
use Rubix\ML\Regressors\KNNRegressor;
use Rubix\ML\Serializers\Native;
use Rubix\ML\Serializers\RBX;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = new Users();
    $rows = $users->getRandomRows();

    // Prepare training data
    $samples = [];
    $labels = [];

    foreach ($rows as $row) {
        $samples[] = [$row[1], $row[4], $row[7], $row[8]]; // Select relevant features
        $labels[] = isset($_POST['labels'][$row[0]]) ? 'fake' : 'valid';
    }

    $dataset = new Labeled($samples, $labels);
    
    // Apply One-Hot Encoding to the dataset
    $encoder = new OneHotEncoder();
    $estimator = new KNearestNeighbors(3); 
    $dataset->apply($encoder);

    // Wrap the model and the transformer in a PersistentModel
    $persistentModel = new PersistentModel($estimator, new Filesystem(__DIR__ . '/model/detector.model', true, new Native()));
    $persistentModel->setPreprocessor($encoder);
    
    // Train the model
    $persistentModel->train($dataset);
    
    // Save model
    $persistentModel->save();

    // Redirect to the index page
    header('Location: /');
    exit;
}
