<?php
include 'connection.php';

function getUuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

try {
    $conn->beginTransaction();
    $query = $conn->prepare('SELECT * FROM `users`');
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!$users) {
        echo "No users found\n";
        exit;
    }

    $query = $conn->prepare('UPDATE `users` SET `uuid` = :uuid WHERE `id` = :id');
    $query->bindParam(':uuid', $uuid);
    $query->bindParam(':id', $id);

    foreach ($users as $user) {
        if (!$user['uuid']) {
            $uuid = getUuid();
            $id = $user['id'];
            $query->execute();
        }
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    echo 'Script failed: ' . $e->getMessage();
}