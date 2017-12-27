<?php
include 'connection.php';

$query = $conn->prepare("SELECT * FROM `like.tmp`");
$query->execute();
$likes = $query->fetchAll();

$query = $conn->prepare("INSERT INTO `like` VALUES (:id, :type, :type_id, :user_id, :date_created)");
$query->bindParam(':id', $id);
$query->bindParam(':type', $type);
$query->bindParam(':type_id', $type_id);
$query->bindParam(':user_id', $user_id);
$query->bindParam(':date_created', $date_created);

foreach ($likes as $like) {
    $id = $like['id'];
    $type_id = $like['type_id'];
    $user_id = $like['user_id'];
    $date_created = $like['date_created'];

    if ($like['type'] == 'review') {
        $type = 1;
    } elseif ($like['type'] == 'checkin') {
        $type = 2;
    } elseif ($like['type'] == 'bookmark') {
        $type = 3;
    } elseif ($like['type'] == 'comment') {
        $type = 4;
    } elseif ($like['type'] == 'photo') {
        $type = 5;
    } elseif ($like['type'] == 'restaurant') {
        $type = 6;
    }

    $query->execute();
}

?>