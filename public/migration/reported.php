<?php
include 'connection.php';

$query = $conn->prepare("SELECT * FROM `reported.tmp`");
$query->execute();
$reports = $query->fetchAll();

$query = $conn->prepare("INSERT INTO `reported` VALUES (NULL, :type, :type_id, :reason, :report_status, :reported_by, :date_created, :modified_by, :date_modified)");
$query->bindParam(':type', $type);
$query->bindParam(':type_id', $type_id);
$query->bindParam(':reason', $reason);
$query->bindParam(':report_status', $report_status);
$query->bindParam(':reported_by', $reported_by);
$query->bindParam(':date_created', $date_created);
$query->bindParam(':modified_by', $modified_by);
$query->bindParam(':date_modified', $date_modified);

foreach ($reports as $report) {
    $type_id = $report['type_id'];
    $reason = $report['reason'];
    $report_status = 0;
    $reported_by = $report['reported_by'];
    $date_created = $report['date_created'];
    $modified_by = $report['reported_by'];
    $date_modified = $report['date_created'];

    if ($report['type'] == 'review') {
        $type = 1;
    } elseif ($report['type'] == 'checkin') {
        $type = 2;
    } elseif ($report['type'] == 'bookmark') {
        $type = 3;
    } elseif ($report['type'] == 'comment') {
        $type = 4;
    } elseif ($report['type'] == 'photo') {
        $type = 5;
    } elseif ($report['type'] == 'restaurant') {
        $type = 6;
    }

    $query->execute();
}

$query2 = $conn->prepare("SELECT * FROM `reported_photos`");
$query2->execute();
$reported_photos = $query2->fetchAll();

foreach ($reported_photos as $photo) {
    $type = 5;
    $type_id = $photo['photo_id'];
    $reason = '';
    $report_status = $photo['validity'];
    $reported_by = $photo['user_id'];
    $date_created = $photo['date_created'];
    $modified_by = $photo['user_id'];
    $date_modified = $photo['date_created'];

    $query->execute();
}

?>