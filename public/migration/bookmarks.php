<?php
include 'connection.php';

$query = $conn->prepare("SELECT * FROM `bookmarks.tmp`");
$query->execute();
$bookmarks = $query->fetchAll();

$query = $conn->prepare("INSERT INTO bookmarks VALUES (:id, :restaurant_id, :user_id, :date_created, :date_modified)");
$query->bindParam(':id', $id);
$query->bindParam(':restaurant_id', $restaurant_id);
$query->bindParam(':user_id', $user_id);
$query->bindParam(':date_created', $date_created);
$query->bindParam(':date_modified', $date_modified);

foreach ($bookmarks as $bookmark) {
    $id = $bookmark['id'];
    $restaurant_id = $bookmark['restaurant_id'];
    $user_id = $bookmark['user_id'];
    $date_created = $bookmark['created'];
    $date_modified = $bookmark['created'];

    $query->execute();
}

?>
