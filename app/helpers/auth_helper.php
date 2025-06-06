<?php

function hasPermission($permission) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $db = getDatabase();
    
    $sql = "SELECT rp.permission 
            FROM user_roles ur 
            JOIN role_permissions rp ON ur.role_id = rp.role_id 
            WHERE ur.user_id = :user_id 
            AND rp.permission = :permission";
            
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':permission' => $permission
    ]);
    
    return $stmt->rowCount() > 0;
}

function getUserRoles($userId) {
    $db = getDatabase();
    
    $sql = "SELECT r.* 
            FROM roles r 
            JOIN user_roles ur ON r.id = ur.role_id 
            WHERE ur.user_id = :user_id";
            
    $stmt = $db->prepare($sql);
    $stmt->execute([':user_id' => $userId]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}