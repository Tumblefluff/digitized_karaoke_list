<?php
// Include the database connection
require_once __DIR__ . '/db.php';

/**
 * Hashes a password using a double-hashed username as a salt.
 */
function hashPassword($username, $password) {
    $salt = hash('sha256', hash('sha256', $username));  // Double-hashed username as salt
    return password_hash($password . $salt, PASSWORD_BCRYPT);
}

/**
 * Verifies a password against the stored hash.
 */
function verifyPassword($username, $password, $storedHash) {
    $salt = hash('sha256', hash('sha256', $username));
    return password_verify($password . $salt, $storedHash);
}
?>
