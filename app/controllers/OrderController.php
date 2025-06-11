<?php
class OrderController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect(BASEURL . '/auth');
        }
    }

    public function index() {
        $data['judul'] = 'Pesan Makanan';
        $data['menu'] = $this->model('Menu_model')->getAllMenu();
        
        $this->view('templates/header', $data);
        $this->view('order/index', $data);
        $this->view('templates/footer');
    }

    public function checkout() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cart = [];
            foreach ($_POST['jumlah'] as $id_menu => $jumlah) {
                if ($jumlah > 0) {
                    $cart[$id_menu] = [
                        'jumlah' => $jumlah,
                        'harga' => $_POST['harga'][$id_menu]
                    ];
                }
            }

            if (empty($cart)) {
                die("Keranjang kosong!");
            }

            $orderModel = $this->model('Order_model');
            $result = $orderModel->processCheckout($_SESSION['user_id'], $cart);

            if ($result['status']) {
                $this->redirect(BASEURL . '/order/antrian/' . $result['nomor_antrian']);
            } else {
                die("Checkout Gagal: " . $result['message']);
            }
        }
    }

    public function antrian($nomor_antrian = '') {
        if (empty($nomor_antrian)) {
            $this->redirect(BASEURL . '/home');
        }

        $data['judul'] = 'Nomor Antrian';
        $data['order'] = $this->model('Order_model')->getOrderDetails($nomor_antrian);

        if (!$data['order']) {
            die("Nomor antrian tidak ditemukan.");
        }
        
        $this->view('templates/header', $data);
        $this->view('order/antrian', $data);
        $this->view('templates/footer');
    }
}