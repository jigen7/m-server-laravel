<?php
include 'connection.php';

$query = $conn->prepare("SELECT * FROM `comments.tmp`");
$query->execute();
$comments = $query->fetchAll();

$query = $conn->prepare("INSERT INTO comments VALUES (:id, :type, :type_id, :comment, :status, :user_id, :date_created)");
$query->bindParam(':id', $id);
$query->bindParam(':type', $type);
$query->bindParam(':type_id', $type_id);
$query->bindParam(':comment', $text);
$query->bindParam(':status', $status);
$query->bindParam(':user_id', $user_id);
$query->bindParam(':date_created', $date_created);

foreach ($comments as $comment) {
    $id = $comment['id'];
    $text = $comment['comment'];
    $status = 1;
    $user_id = $comment['user_id'];
    $date_created = $comment['date_created'];

    if ($comment['review_id']) {
        $type = 1;
        $type_id = $comment['review_id'];
    } elseif ($comment['checkin_id']) {
        $type = 2;
        $type_id = $comment['checkin_id'];
    } elseif ($comment['photo_id']) {
        $type = 5;
        $type_id = $comment['photo_id'];
    }

    $query->execute();
}