<?php

require 'vendor/autoload.php';

use App\Users;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Transformers\OneHotEncoder;
use Rubix\ML\Serializers\Native;
use Rubix\ML\Serializers\RBX;

function checkIfAccountIsFake($username, $email)
{
    // Load the trained model
    $users = new Users();
    $persistentModel  = PersistentModel::load(new Filesystem($users::MODEL_PATH), new RBX());

    // Extract features from the username and email
    $preferredLangcode = detectLangcode($email); // Simplified assumption
    $timezone = 'Europe/Madrid'; // Assuming Spain for simplicity
    $access = 0; // You can adjust these values based on real data
    $login = 0;  // You can adjust these values based on real data

    // Prepare the sample
    $sample = [[$preferredLangcode, $timezone, $access, $login]];

    // One-hot encode the sample (same way as during training)
    $dataset = new Unlabeled($sample);

    // Predict if the account is fake
    $prediction = $persistentModel->predict($dataset)[0];

    return $prediction === 'fake';
}

function detectLangcode($email)
{
    // A simple example to detect language from email domain
    $domain = substr(strrchr($email, "@"), 1);
    if ($domain === 'gmail.com' || $domain === 'hotmail.com') {
        return 'en';
    }
    // You can expand this to cover more domains
    return 'es'; // default to Spanish
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Account</title>
</head>
<body>
    <h1>Check if an Account is Fake</h1>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        
        <button type="submit">Check</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Process the form submission
        $username = $_POST['username'];
        $email = $_POST['email'];

        // Call the function to check if the account is fake
        $isFake = checkIfAccountIsFake($username, $email);

        if ($isFake) {
            echo "<p style='color:red;'>This account is likely fake.</p>";
        } else {
            echo "<p style='color:green;'>This account appears to be valid.</p>";
        }
    }
    ?>
</body>
</html>

