<?php
require 'config.php';

$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$validSorts = [
    'name' => 'name',
    'neighbourhood_group_cleansed' => 'neighbourhood_group_cleansed',
    'price' => 'price',
    'host_name' => 'host_name'
];

$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $validSorts)
    ? $_GET['sort']
    : 'name';

$order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc'
    ? 'DESC'
    : 'ASC';

$offset = ($page - 1) * $perPage;

$totalStmt = $dbh->query("SELECT COUNT(*) FROM listings");
$total = (int)$totalStmt->fetchColumn();
$totalPages = (int)ceil($total / $perPage);
 
$sortColumn = $validSorts[$sort];

$sql = "SELECT id, name, picture_url, host_name, price, neighbourhood_group_cleansed
        FROM listings
        ORDER BY $sortColumn $order
        LIMIT :limit OFFSET :offset";

$stmt = $dbh->prepare($sql);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$listings = $stmt->fetchAll();

function buildUrl($params = []) {
    $base = strtok($_SERVER['REQUEST_URI'], '?');
    $current = $_GET;
    foreach ($params as $k => $v) {
        $current[$k] = $v;
    }
    return $base . '?' . http_build_query($current);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste Airbnb</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial; padding:20px; }
        .card { border:1px solid #ddd; padding:12px; margin-bottom:12px; display:flex; gap:12px; }
        .card img { width:180px; height:120px; object-fit:cover; border-radius:6px; }
        .meta { flex:1; }
        .pagination a { margin:0 5px; text-decoration:none; }
        .current { color:#fff; background:red; padding:4px 8px; border-radius:5px; }
    </style>
</head>
<body>

<h1>Liste des logements</h1>

<form method="get">
    <label>Trier par :</label>
    <select name="sort" onchange="this.form.submit()">
        <option value="name" <?= $sort=='name'?'selected':'' ?>>Nom</option>
        <option value="neighbourhood_group_cleansed" <?= $sort=='neighbourhood_group_cleansed'?'selected':'' ?>>Ville</option>
        <option value="price" <?= $sort=='price'?'selected':'' ?>>Prix</option>
        <option value="host_name" <?= $sort=='host_name'?'selected':'' ?>>Propriétaire</option>
    </select>

    <select name="order" onchange="this.form.submit()">
        <option value="asc" <?= $order=='ASC'?'selected':'' ?>>Ascendant</option>
        <option value="desc" <?= $order=='DESC'?'selected':'' ?>>Descendant</option>
    </select>
</form>

<?php foreach ($listings as $l): ?>
    <div class="card">
        <img src="<?= htmlspecialchars($l['picture_url']) ?>" alt="">
        <div class="meta">
            <h2><?= htmlspecialchars($l['name']) ?></h2>
            <p><strong>Ville : </strong><?= htmlspecialchars($l['neighbourhood_group_cleansed']) ?></p>
            <p><strong>Propriétaire : </strong><?= htmlspecialchars($l['host_name']) ?></p>
            <p><strong>Prix : </strong><?= htmlspecialchars($l['price']) ?> €</p>
        </div>
    </div>
<?php endforeach; ?>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="<?= buildUrl(['page' => $page-1]) ?>">&laquo; Précédent</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $page): ?>
            <span class="current"><?= $i ?></span>
        <?php else: ?>
            <a href="<?= buildUrl(['page'=>$i]) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="<?= buildUrl(['page' => $page+1]) ?>">Suivant &raquo;</a>
    <?php endif; ?>
</div>

<br>
<a href="add.php">Ajouter une annonce</a>

</body>
</html>
