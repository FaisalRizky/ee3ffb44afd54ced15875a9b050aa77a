<?php

namespace Controllers;

class UserController
{
    public function listUsers()
    {
        // Logic to list users
        header('Content-Type: application/json');
        echo json_encode(['users' => ['User1', 'User2']]);
    }

    public function createUser()
    {
        // Logic to create a new user
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function viewUser($id)
    {
        // Logic to view a specific user
        header('Content-Type: text/html');
        echo '<h1>View User</h1>';
        echo '<p>User ' . htmlspecialchars($id) . '</p>';
    }
}
