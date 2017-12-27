<?php
include 'connection.php';

$query = $conn->prepare("SELECT * FROM `reviews.tmp`");
$query->execute();
$reviews = $query->fetchAll();

$query = $conn->prepare("INSERT INTO reviews VALUES (:id, :restaurant_id, :rating, :title, :text, :status, :points, :user_id, :date_created, :date_modified)");
$query->bindParam(':id', $id);
$query->bindParam(':restaurant_id', $restaurant_id);
$query->bindParam(':rating', $rating);
$query->bindParam(':title', $title);
$query->bindParam(':text', $text);
$query->bindParam(':status', $status);
$query->bindParam(':points', $points);
$query->bindParam(':user_id', $user_id);
$query->bindParam(':date_created', $date_created);
$query->bindParam(':date_modified', $date_modified);

foreach ($reviews as $review) {
    $id = $review['id'];
    $restaurant_id = $review['restaurant_id'];
    $rating = $review['rating'];
    $title = $review['title'];
    $text = $review['text'];
    $status = 1;
    $points = $review['points'];
    $user_id = $review['user_id'];
    $date_created = $review['created'];
    $date_modified = $review['created'];

    $query->execute();
}

?>
