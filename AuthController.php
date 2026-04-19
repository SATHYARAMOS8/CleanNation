<?php
/**
 * Auth Controller
 */
require_once __DIR__ . '/../core/middleware.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Pickup.php';

class AuthController {
    private $pdo;
    private $user;
    private $pickup;
    
    public function __construct($pdo) {
        runMiddleware();
        $this->pdo = $pdo;
        $this->user = new User($pdo);
        $this->pickup = new Pickup($pdo);
    }
    
    public function login() {
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        if (isPost()) {
            if (!verifyCsrfToken(input('csrf_token'))) {
                setFlash('danger', 'Invalid form submission.');
                redirect('login');
            }

            $username = input('username');
            $password = input('password');

            $user = $this->user->findByUsername($username);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                if ($user) {
                    $this->user->incrementLoginAttempts($user['id']);
                }
                setFlash('danger', 'Invalid username or password');
                redirect('login');
            }

            if ($this->user->isLocked($user)) {
                setFlash('danger', 'Account locked. Try again later.');
                redirect('login');
            }

            $this->user->resetLoginAttempts($user['id']);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            setFlash('success', 'Welcome back!');

            if ($user['role'] === 'admin') {
                redirect('admin');
            }
            redirect('dashboard');
        }

        view('login', ['title' => 'Login']);
    }

    public function register() {
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        if (isPost()) {
            if (!verifyCsrfToken(input('csrf_token'))) {
                setFlash('danger', 'Invalid form submission.');
                redirect('register');
            }

            $username = input('username');
            $password = input('password');
            $confirm_password = input('confirm_password');
            $role = input('role');

            if (empty($username) || empty($password) || empty($confirm_password) || empty($role)) {
                setFlash('danger', 'All fields are required.');
            } elseif (!in_array($role, ['admin', 'customer', 'driver'])) {
                setFlash('danger', 'Invalid role selected.');
            } elseif ($role === 'admin' && $this->user->hasAdmin()) {
                setFlash('danger', 'An admin account already exists. Only one admin allowed.');
            } elseif ($password !== $confirm_password) {
                setFlash('danger', 'Passwords do not match.');
            } elseif (strlen($password) < 8) {
                setFlash('danger', 'Password must be at least 8 characters.');
            } else {
                if ($this->user->findByUsername($username)) {
                    setFlash('danger', 'Username is already taken.');
                } elseif ($this->user->create($username, $password, $role)) {
                    if ($role === 'driver') {
                        $stmt = $this->pdo->prepare('INSERT INTO drivers (user_id) VALUES (LAST_INSERT_ID())');
                        $stmt->execute();
                    }
                    setFlash('success', 'Registration successful. Please login.');
                    redirect('login');
                } else {
                    setFlash('danger', 'Unable to create account at this time.');
                }
            }
        }

        view('register', ['title' => 'Register']);
    }

    public function dashboard() {
        if (!isLoggedIn()) {
            redirect('login');
        }

        $role = $_SESSION['role'] ?? 'customer';

        if ($role === 'admin') {
            redirect('admin');
        }

        if ($role === 'driver') {
            $driver = $this->user->findByUsername($_SESSION['username']);
            $pickupList = [];
            if ($driver) {
                $stmt = $this->pdo->prepare('SELECT id FROM drivers WHERE user_id = ? LIMIT 1');
                $stmt->execute([$driver['id']]);
                $driverRecord = $stmt->fetch();
                if ($driverRecord) {
                    $pickupList = $this->pickup->getByDriverId($driverRecord['id']);
                }
            }
            view('driver/dashboard', ['title' => 'Driver Dashboard', 'pickups' => $pickupList]);
            return;
        }

        if ($role === 'customer') {
            $customerId = $_SESSION['user_id'];
            $pickupList = $this->pickup->getByUser($customerId);
            view('customer/dashboard', ['title' => 'Customer Dashboard', 'pickups' => $pickupList]);
            return;
        }

        view('customer/dashboard', ['title' => 'Customer Dashboard', 'pickups' => []]);
    }

    public function createPickup() {
        if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== 'customer') {
            redirect('login');
        }

        if (!isPost()) {
            redirect('dashboard');
        }

        if (!verifyCsrfToken(input('csrf_token'))) {
            setFlash('danger', 'Invalid form submission.');
            redirect('dashboard');
        }

        $data = [
            'user_id' => $_SESSION['user_id'],
            'name' => input('name'),
            'phone' => input('phone'),
            'email' => input('email'),
            'address' => input('address'),
            'pickup_date' => input('pickup_date'),
            'waste_type' => input('waste_type'),
        ];

        if (empty($data['name']) || empty($data['phone']) || empty($data['email']) || empty($data['address']) || empty($data['pickup_date'])) {
            setFlash('danger', 'All pickup fields are required.');
            redirect('dashboard');
        }

        if ($this->pickup->create($data)) {
            setFlash('success', 'Pickup request created successfully.');
        } else {
            setFlash('danger', 'Could not create pickup request.');
        }

        redirect('dashboard');
    }

    public function completePickup() {
        if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== 'driver') {
            redirect('login');
        }

        if (!isPost()) {
            redirect('dashboard');
        }

        if (!verifyCsrfToken(input('csrf_token'))) {
            setFlash('danger', 'Invalid form submission.');
            redirect('dashboard');
        }

        $pickupId = input('pickup_id');

        $stmt = $this->pdo->prepare('SELECT id FROM drivers WHERE user_id = ? LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        $driver = $stmt->fetch();

        if (!$driver) {
            setFlash('danger', 'Driver profile not found.');
            redirect('dashboard');
        }

        if ($this->pickup->complete($pickupId, $driver['id'])) {
            setFlash('success', 'Pickup marked complete.');
        } else {
            setFlash('danger', 'Unable to complete pickup.');
        }

        redirect('dashboard');
    }
    
    public function logout() {
        session_destroy();
        setFlash('info', 'Logged out successfully');
        redirect('login');
    }
}


?>


