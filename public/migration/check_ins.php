<?php
include 'connection.php';

$query = $conn->prepare("SELECT * FROM `check_ins.tmp`");
$query->execute();
$check_ins = $query->fetchAll();

$query = $conn->prepare("INSERT INTO check_ins VALUES (:id, :restaurant_id, :message, :points, :latitude, :longitude, :user_id, :date_created, :date_modified)");
$query->bindParam(':id', $id);
$query->bindParam(':restaurant_id', $restaurant_id);
$query->bindParam(':message', $message);
$query->bindParam(':points', $points);
$query->bindParam(':latitude', $latitude);
$query->bindParam(':longitude', $longitude);
$query->bindParam(':user_id', $user_id);
$query->bindParam(':date_created', $date_created);
$query->bindParam(':date_modified', $date_modified);

foreach ($check_ins as $check_in) {
    $id = $check_in['id'];
    $restaurant_id = $check_in['restaurant_id'];
    $message = $check_in['message'];
    $points = $check_in['points'];
    $latitude = 0;
    $longitude = 0;
    $user_id = $check_in['user_id'];
    $date_created = $check_in['created'];
    $date_modified = $check_in['created'];

    $query->execute();
}

?>
