<?php

namespace App\Controllers;

use App\Models\User;
use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $template = 'home';
        $data = [
            'student' => 'Rafael John L. Castro',
            'title' => 'IPT10 Laboratory Activity #10',
            'college' => 'College of Computer Studies',
            'university' => 'Angeles University Foundation',
            'location' => 'Angeles City, Pampanga, Philippines'
        ];
        $output = $this->render($template, $data);
        return $output;
    }

    public function welcome()
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the user is logged in
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            header("Location: /login-form");
            exit;
        }

        // Retrieve user data
        $user = new User();
        $users = $user->getAllUsers();

        // Retrieve username for the welcome message
        $username = $_SESSION['username'];

        // Format user data (concatenating first and last names)
        foreach ($users as &$user) {
            $user['complete_name'] = $user['first_name'] . ' ' . $user['last_name'];
        }

        $template = 'welcome';
        $data = [
            'title' => 'Welcome',
            'message' => "Welcome to IPT10, $username!",
            'data' => $users // Pass the formatted user data to the template
        ];

        $output = $this->render($template, $data);
        return $output;
    }
}
