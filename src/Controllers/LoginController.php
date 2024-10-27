<?php

namespace App\Controllers;

use App\Models\User;

class LoginController extends BaseController {

    public function showLoginForm() {
        // Start the session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize login attempts in the session
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }

        // Retrieve logout message and clear it from the session
        $logout_message = $_SESSION['logout_message'] ?? null;
        unset($_SESSION['logout_message']); // Clear the message after using it

        $attempts = $_SESSION['login_attempts'];
        $form_disabled = $attempts >= 3; // Disable form if attempts exceed 3

        // Prepare data to render the login form
        $data = [
            'remaining_attempts' => 3 - $attempts,
            'form_disabled' => $form_disabled,
            'timer' => ($form_disabled) ? 30 : null, // Set timer only if the form is disabled
            'logout_message' => $logout_message // Pass the logout message
        ];

        return $this->render('login-form', $data);
    }

    public function login() {
        // Start the session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? null;
            $password = $_POST['password'] ?? null;

            // Validate user input
            if (empty($username) || empty($password)) {
                $errors[] = "Username and password are required.";
                return $this->showLoginFormWithErrors($errors);
            }

            $user = new User();
            $saved_password_hash = $user->getPassword($username); // Retrieve saved password hash

            // Verify provided password against saved hash
            if ($saved_password_hash && password_verify($password, $saved_password_hash)) {
                // Successful login
                $_SESSION['login_attempts'] = 0; // Reset attempts on successful login
                $_SESSION['is_logged_in'] = true; // Set logged-in session variable
                $_SESSION['username'] = $username; // Store username in session
                header("Location: /welcome"); // Redirect to welcome page
                exit;
            } else {
                // Increment login attempts on failure
                $_SESSION['login_attempts']++;
                $remaining_attempts = 3 - $_SESSION['login_attempts'];
                $errors[] = "Invalid username or password. Attempts remaining: $remaining_attempts.";
                return $this->showLoginFormWithErrors($errors, $remaining_attempts);
            }
        } else {
            return $this->showLoginForm(); // Show the login form for GET requests
        }
    }

    private function showLoginFormWithErrors($errors, $remaining_attempts = null) {
        $form_disabled = ($remaining_attempts !== null && $remaining_attempts <= 0); // Disable form if attempts exhausted

        return $this->render('login-form', [
            'errors' => $errors,
            'remaining_attempts' => $remaining_attempts,
            'form_disabled' => $form_disabled, // Pass form_disabled flag
            'timer' => ($form_disabled) ? 30 : null // Set timer if the form is disabled
        ]);
    }

    public function logout() {
        // Start the session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Set logout message
        $_SESSION['logout_message'] = "You have been logged out successfully.";

        // Redirect to login form
        header("Location: /login-form");
        exit;
    }
}
