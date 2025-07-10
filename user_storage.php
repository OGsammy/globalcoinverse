<?php
// user_storage.php - file-based user data storage

define('USER_DATA_FILE', __DIR__ . '/users.json');

/**
 * Get all users from the JSON file.
 * @return array
 */
function get_users() {
    if (!file_exists(USER_DATA_FILE)) {
        file_put_contents(USER_DATA_FILE, json_encode([]));
    }
    $json = file_get_contents(USER_DATA_FILE);
    $users = json_decode($json, true);
    if (!is_array($users)) {
        $users = [];
    }
    return $users;
}

/**
 * Save all users to the JSON file.
 * @param array $users
 * @return bool
 */
function save_users(array $users) {
    $json = json_encode($users, JSON_PRETTY_PRINT);
    return file_put_contents(USER_DATA_FILE, $json) !== false;
}

/**
 * Find a user by username or email.
 * @param string $identity
 * @return array|null
 */
function find_user_by_identity($identity) {
    $users = get_users();
    foreach ($users as $user) {
        if (strcasecmp($user['username'], $identity) === 0 || strcasecmp($user['email'], $identity) === 0) {
            return $user;
        }
    }
    return null;
}

/**
 * Find a user by username or email and return its index in the array.
 * @param string $identity
 * @return int|null
 */
function find_user_index_by_identity($identity) {
    $users = get_users();
    foreach ($users as $index => $user) {
        if (strcasecmp($user['username'], $identity) === 0 || strcasecmp($user['email'], $identity) === 0) {
            return $index;
        }
    }
    return null;
}
?>
