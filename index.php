<?php

require 'vendor/autoload.php';

use App\Users;
$users = new Users();
$rows = $users->getRandomRows();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fake Account Detector</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Fake Account Detector</h1>
        <form method="post" action="src/TrainModel.php">
            <table class="table">
                <thead>
                <tr>
                    <th>UID</th>
                    <th>Preferred Language</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Timezone</th>
                    <th>Created</th>
                    <th>Changed</th>
                    <th>Access</th>
                    <th>Login</th>
                    <th>Prediction</th>
                    <th>Label</th>
                </tr>
                </thead>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($row as $value): ?>
                            <td><?= htmlspecialchars($value) ?></td>
                        <?php endforeach; ?>
                        <?php $isFake = $users->isFakeAccount($row); ?>
                        <td><?= $isFake ? 'Fake' : 'Valid' ?></td>
                        <td><input type="checkbox" name="labels[<?= $row[0] ?>]" value="fake" <?= $isFake ? 'checked' : '' ?>></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div class="control">
                <button type="submit" class="button is-link">Train Model</button>
            </div>
        </form>
    </div>
    <br /><br />
</body>
</html>
