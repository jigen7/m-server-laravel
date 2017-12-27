<?php
include 'connection.php';

$query = $conn->prepare("SELECT * FROM `photos.tmp`");
$query->execute();
$photos = $query->fetchAll();

$query = $conn->prepare("INSERT INTO photos VALUES (:id, :type, :type_id, :restaurant_id, :url, :text, :status, :points, :user_id, :date_uploaded)");
$query->bindParam(':id', $id);
$query->bindParam(':type', $type);
$query->bindParam(':type_id', $type_id);
$query->bindParam(':restaurant_id', $restaurant_id);
$query->bindParam(':url', $url);
$query->bindParam(':text', $text);
$query->bindParam(':status', $status);
$query->bindParam(':points', $points);
$query->bindParam(':user_id', $user_id);
$query->bindParam(':date_uploaded', $date_uploaded);

foreach ($photos as $photo) {
    $id = $photo['id'];
    $restaurant_id = $photo['restaurant_id'];
    $url = $photo['url'];
    $text = '';
    $status = $photo['valid'];
    $points = $photo['points'];
    $user_id = $photo['user_id'];
    $date_uploaded = $photo['date_uploaded'];

    if ($photo['review_id']) {
        $type = 1;
        $type_id = $photo['review_id'];
    } elseif ($photo['checkin_id']) {
        $type = 2;
        $type_id = $photo['checkin_id'];
    }

    $query->execute();
}

?>
