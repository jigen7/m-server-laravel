<?php
include 'connection.php';

$query = $conn->prepare("SELECT * FROM `activities.tmp`");
$query->execute();
$activities = $query->fetchAll();

$query = $conn->prepare("INSERT INTO activities VALUES (:id, :type, :type_id, :restaurant_id, :user_id, :date_created)");
$query->bindParam(':id', $id);
$query->bindParam(':type', $type);
$query->bindParam(':type_id', $type_id);
$query->bindParam(':restaurant_id', $restaurant_id);
$query->bindParam(':user_id', $user_id);
$query->bindParam(':date_created', $date_created);

foreach ($activities as $activity) {
    $id = $activity['id'];
    $type_id = $activity['type_id'];
    $restaurant_id = $activity['restaurant_id'];
    $user_id = $activity['user_id'];
    $date_created = $activity['date_created'];

    if ($activity['type'] == 'Reviews') {
        $type = 1;
    } elseif ($activity['type'] == 'CheckIn') {
        $type = 2;
    } elseif ($activity['type'] == 'Bookmarks') {
        continue;
    }

    $query->execute();
}

?>
