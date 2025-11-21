<?php
require 'config.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $picture_url = trim($_POST['picture_url']);
    $host_name = trim($_POST['host_name']);
    $price = (int)$_POST['price'];
    $neighbourhood = trim($_POST['neighbourhood']);

    if ($name == '') $errors[] = "Nom requis";
    if ($host_name == '') $errors[] = "Propriétaire requis";
    if ($price <= 0) $errors[] = "Prix invalide";

    if (empty($errors)) {
        $stmt = $dbh->prepare("
            INSERT INTO listings (name, picture_url, host_name, price, neighbourhood_group_cleansed)
            VALUES (:n, :p, :h, :pr, :nb)
        ");
        $stmt->execute([
            ':n' => $name,
            ':p' => $picture_url,
            ':h' => $host_name,
            ':pr' => $price,
            ':nb' => $neighbourhood
        ]);

        header("Location: index.php?added=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ajouter</title>
    <style>
        *{
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: "Inter", Arial, sans-serif;
            background: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

    h1 {
        text-align: center;
        font-size: 28px;
        margin-bottom: 10px;
    }

    a{
        text-decoration: none;
        color: #666;
        font-size: 14px;
    }

    a:hover {
        color: #000;
    }

    .container {
        background: #fff;
        padding: 35px 40px;
        margin-top: 40px;
        width: 420px;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }

    form label {
        display: block;
        font-weight: 600;
        margin-bottom: 1px;
    }

    form input {
        width: 100%;
        padding: 12px;
        border: 1px solid #d3d3d3;
        border-radius: 8px;
    transition: 0.2s;
    font-size: 12px;
}

form input:focus {
    border-color: #7a5cff;
    box-shadow: 0 0 0 3px rgba(122, 92, 255, 0.2);
    outline: none;
}

button {
    width: 100%;
    padding: 14px;
    background: #7a5cff;
    color: white;
    border: none;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.2s;
}

button:hover {
    background: #634be8;
}

ul {
    padding-left: 20px;
}
ul li {
    font-size: 14px;
    margin-bottom: 6px;
}

    </style>
</head>
<body>

<div class="container">
<h1>Ajouter une annonce</h1>
<a href="index.php">Retour</a>

<?php if (!empty($errors)): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post">
    <label>Nom : <input type="text" name="name"></label><br><br>
    <label>URL image : <input type="text" name="picture_url"></label><br><br>
    <label>Propriétaire : <input type="text" name="host_name"></label><br><br>
    <label>Prix (€) : <input type="number" name="price"></label><br><br>
    <label>Ville : <input type="text" name="neighbourhood"></label><br><br>

    <button type="submit">Ajouter</button>
</form>
</div>
</body>
</html>
