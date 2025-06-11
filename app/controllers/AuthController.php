<?php
class AuthController extends Controller {
    public function index() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect(BASEURL . '/home');
        }
        $data['judul'] = 'Login';
        $this->view('templates/header', $data);
        $this->view('auth/login', $data);
        $this->view('templates/footer');
    }

    public function register() {
        $data['judul'] = 'Register';
        $this->view('templates/header', $data);
        $this->view('auth/register', $data);
        $this->view('templates/footer');
    }

    public function prosesRegister() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User_model');
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'nim' => trim($_POST['nim']),
                'nama' => trim($_POST['nama']),
                'password' => trim($_POST['password']),
                'password_confirm' => trim($_POST['password_confirm'])
            ];

            if ($data['password'] !== $data['password_confirm']) {
                die('Password tidak cocok!');
            }
            if ($userModel->findUserByNim($data['nim'])) {
                die('NIM sudah terdaftar!');
            }

            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            if ($userModel->register($data)) {
                $this->redirect(BASEURL . '/auth');
            } else {
                die('Registrasi gagal!');
            }
        }
    }
    
    public function prosesLogin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User_model');
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'nim' => trim($_POST['nim']),
                'password' => trim($_POST['password'])
            ];
            
            $user = $userModel->findUserByNim($data['nim']);
            if ($user && password_verify($data['password'], $user['password'])) {
                // Buat Session
                $_SESSION['user_id'] = $user['id_mahasiswa'];
                $_SESSION['user_nama'] = $user['nama'];
                $this->redirect(BASEURL . '/home');
            } else {
                die('NIM atau Password salah');
            }
        }
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_nama']);
        session_destroy();
        $this->redirect(BASEURL . '/auth');
    }
}