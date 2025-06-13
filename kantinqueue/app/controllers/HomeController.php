<?php
class HomeController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect(BASEURL . '/auth');
        }
    }

    public function index() {
        $data['judul'] = 'Home';
        $data['nama'] = $_SESSION['user_nama'];
        
        $this->view('templates/header', $data);
        $this->view('home/index', $data);
        $this->view('templates/footer');
    }
}