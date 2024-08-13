<?php

namespace App;

use Rubix\ML\Persisters\Filesystem;

class Users
{
    private $data;
    private string $filePath;
    private string $modelPath;

    // Define constants for file paths
    const DATA_PATH = __DIR__.'/../data/users.csv';
    const MODEL_PATH = __DIR__.'/../model/detector.model';

    public function __construct()
    {
        $this->filePath = self::DATA_PATH;
        $this->modelPath = self::MODEL_PATH;
        $this->loadData();
    }

    private function loadData()
    {

        if (!file_exists($this->filePath) || !is_readable($this->filePath)) {
            throw new \Exception("CSV file not found or is not readable.");
        }

        $this->data = array_map('str_getcsv', file($this->filePath));
        array_shift($this->data); // remove header row
    }

    public function getRandomRows($count = 30)
    {
        $keys = array_rand($this->data, $count);
        $rows = array_map(function($key) {
            return $this->data[$key];
        }, $keys);

        return $rows;
    }

    // Extend the Users class above with the following methods

    public function heuristicIsFakeAccount($row)
    {
        // Explicit features
        if ($row[1] === 'en' && $row[4] === 'Europe/Madrid') return true;
        if ($row[7] == 0 || $row[8] == 0) return true;

        // Implicit features
        if (preg_match('/\d/', $row[2])) return true;
        if (preg_match('/(\.ru|\.pl|\.ltd|\.biz)$/', $row[3])) return true;
        if (preg_match('/(\.\w\.)/', $row[3])) return true;

        return false;
    }
    public function modelIsFakeAccount($row)
    {
        // Check if the model exists
        if (!file_exists($this->modelPath)) {
            return $this->heuristicIsFakeAccount($row);
        }

        // Load the model
        $persister = new Filesystem($this->modelPath);
        $estimator = $persister->load();

        // Select relevant features
        $sample = [$row[1], $row[4], $row[7], $row[8]];
        $prediction = $estimator->predictSample($sample);

        return $prediction === 'fake';
    }

    // Unified method to decide which detection method to use
    public function isFakeAccount($row)
    {
        if (file_exists($this->modelPath)) {
            return $this->modelIsFakeAccount($row);
        }

        return $this->heuristicIsFakeAccount($row);
    }

}
