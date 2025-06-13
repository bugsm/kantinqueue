<?php
class AuthController extends Controller {
    public function index() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/home');
            exit;
        }
        $data['judul'] = 'Login';
        $this->view('templates/header', $data);
        $this->view('auth/login', $data);
        $this->view('templates/footer');
    }

    public function register() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/home');
            exit;
        }
        $data['judul'] = 'Register';
        $this->view('templates/header', $data);
        $this->view('auth/register', $data);
        $this->view('templates/footer');
    }

    public function prosesLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Start output buffering
            ob_start();
            
            try {
                // Get and sanitize input
                $nim = htmlspecialchars(trim($_POST['nim']));
                $password = $_POST['password'];

                if (empty($nim) || empty($password)) {
                    throw new Exception('NIM dan password harus diisi');
                }

                $user = $this->model('User_model')->findUserByNim($nim);
                
                if (!$user) {
                    throw new Exception('NIM tidak ditemukan');
                }

                if (!password_verify($password, $user['password'])) {
                    throw new Exception('Password yang Anda masukkan salah');
                }

                // Set session
                $_SESSION['user_id'] = $user['id_mahasiswa'];
                $_SESSION['user_nim'] = $user['nim'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['user_role'] = $user['role'];

                // Clear output buffer
                ob_end_clean();
                
                // Send JSON response
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login berhasil',
                    'redirect' => BASEURL . '/home'
                ]);
                exit;
                
            } catch (Exception $e) {
                // Clear output buffer
                ob_end_clean();
                
                // Send JSON response
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
                exit;
            }
        }
    }

    public function prosesRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Start output buffering
            ob_start();
            
            try {
                // Get and sanitize input
                $nim = htmlspecialchars(trim($_POST['nim']));
                $nama = htmlspecialchars(trim($_POST['nama']));
                $password = $_POST['password'];
                $konfirmasi_password = $_POST['konfirmasi_password'];

                // Validasi input kosong
                if (empty($nim) || empty($nama) || empty($password) || empty($konfirmasi_password)) {
                    throw new Exception('Semua field harus diisi');
                }

                // Validasi NIM harus angka
                if (!is_numeric($nim)) {
                    throw new Exception('NIM harus berupa angka');
                }

                // Validasi panjang NIM (sesuaikan dengan kebutuhan)
                if (strlen($nim) < 8 || strlen($nim) > 15) {
                    throw new Exception('NIM harus terdiri dari 8-15 digit angka');
                }

                // Validasi password
                if (strlen($password) < 6) {
                    throw new Exception('Password minimal 6 karakter');
                }

                // Validasi konfirmasi password
                if ($password !== $konfirmasi_password) {
                    throw new Exception('Password dan konfirmasi password tidak cocok');
                }

                $userModel = $this->model('User_model');
                
                // Cek NIM sudah terdaftar
                if ($userModel->findUserByNim($nim)) {
                    throw new Exception('NIM ' . $nim . ' sudah terdaftar sebagai akun');
                }

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                if ($userModel->register([
                    'nim' => $nim,
                    'nama' => $nama,
                    'password' => $hashedPassword
                ])) {
                    // Clear output buffer
                    ob_end_clean();
                    
                    // Send JSON response
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Registrasi berhasil. Silakan login.',
                        'redirect' => BASEURL . '/auth'
                    ]);
                    exit;
                } else {
                    throw new Exception('Gagal melakukan registrasi');
                }
            } catch (Exception $e) {
                // Clear output buffer
                ob_end_clean();
                
                // Send JSON response
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
                exit;
            }
        }
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASEURL . '/auth');
        exit;
    }
}