<?php
include 'connection.php';

$query = $conn->prepare("SELECT * FROM `restaurants.tmp`");
$query->execute();
$restaurants = $query->fetchAll();

$query = $conn->prepare("INSERT INTO restaurants VALUES (:id, :name, :slug_name, :address, :telephone, :budget, :rating, :view_count, :operating_time,
    :latitude, :longitude, :thumbnail, :credit_card, :smoking, :is_24hours, :can_dinein, :can_dineout,
    :can_deliver, :status_close, :status_verify, :user_id)");
$query->bindParam(':id', $id);
$query->bindParam(':name', $name);
$query->bindParam(':slug_name', $name);
$query->bindParam(':address', $address);
$query->bindParam(':telephone', $telephone);
$query->bindParam(':budget', $budget);
$query->bindParam(':rating', $rating);
$query->bindParam(':view_count', $view_count);
$query->bindParam(':operating_time', $operating_time);
$query->bindParam(':latitude', $latitude);
$query->bindParam(':longitude', $longitude);
$query->bindParam(':thumbnail', $thumbnail);
$query->bindParam(':credit_card', $credit_card);
$query->bindParam(':smoking', $smoking);
$query->bindParam(':is_24hours', $is_24hours);
$query->bindParam(':can_dinein', $can_dinein);
$query->bindParam(':can_dineout', $can_dineout);
$query->bindParam(':can_deliver', $can_deliver);
$query->bindParam(':status_close', $status_close);
$query->bindParam(':status_verify', $status_verify);
$query->bindParam(':user_id', $user_id);

foreach ($restaurants as $restaurant) {
    $id = $restaurant['id'];
    $name = $restaurant['name'];
    $slug_name = '';
    $address = $restaurant['address'];
    $telephone = $restaurant['telephone'];
    $budget = $restaurant['budget'];
    $rating = $restaurant['rating'];
    $view_count = $restaurant['viewcount'];
    if($restaurant['operating_from'] && $restaurant['operating_to']) {
        $operating_time = $restaurant['operating_from'] . " - " . $restaurant['operating_to'];
    } else {
        $operating_time = '';
    }
    $latitude = $restaurant['latitude'];
    $longitude = $restaurant['longitude'];
    $thumbnail = $restaurant['thumbnail'];
    $credit_card = $restaurant['credit_card'];
    $smoking = $restaurant['smoking'];
    $is_24hours = $restaurant['is_24hours'];
    $can_dinein = $restaurant['can_dinein'];
    $can_dineout = $restaurant['can_dineout'];
    $can_deliver = $restaurant['can_deliver'];
    $status_close = $restaurant['status_close'];
    $status_verify = $restaurant['status_verify'];
    $user_id = $restaurant['user_id'];
    $query->execute();
}

?>
