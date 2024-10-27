<?php 

namespace App\Controllers;

use App\Models\User;

// require_once '../../vendor/autoload.php';


class RegistrationController extends BaseController
{

    public function index() {

        $template = 'registration-form';


        $output = $this->render($template);

        return $output;
    }

    public function register() {
        // Enable error reporting for debugging
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        try {
            // Retrieve form data
            $username = $_POST['username'];
            $email = $_POST['email'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $password = $_POST['password'];
            $password_confirmation = $_POST['password_confirmation'];

            // Required field check
            if (empty($username) || empty($email) || empty($password) || empty($password_confirmation)) {
                echo "All required fields must be filled out.";
                return;
            }

            // Password length check
            if (strlen($password) < 8) {
                echo "Password must be at least 8 characters long.";
                return;
            }

            // Numeric character check
            if (!preg_match('/[0-9]/', $password)) {
                echo "Password must contain at least one numeric character.";
                return;
            }

            // Non-numeric character check
            if (!preg_match('/[a-zA-Z]/', $password)) {
                echo "Password must contain at least one non-numeric character.";
                return;
            }

            // Special character check
            if (!preg_match('/[\W]/', $password)) {
                echo "Password must contain at least one special character (!@#$%^&*-+).";
                return;
            }

            // Password confirmation check
            if ($password !== $password_confirmation) {
                echo "Passwords do not match.";
                return;
            }

            // If all checks pass, save the data using the User model
            $user = new User();
            $save_result = $user->save($username, $email, $first_name, $last_name, $password);

            if ($save_result > 0) {
                return $this->render('success'); 
            } else {
                echo "Error during registration. Please try again.";
            }

        } catch (Exception $e) {
            // Catch and display any errors
            echo "Error: " . $e->getMessage();
        }
    }
}

