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
            // Start output buffering
            ob_start();
            
            try {
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
                    throw new Exception('Keranjang masih kosong! Silakan pilih menu terlebih dahulu.');
                }

                $orderModel = $this->model('Order_model');
                $result = $orderModel->processCheckout($_SESSION['user_id'], $cart);

                if ($result['status']) {
                    // Clear output buffer
                    ob_end_clean();
                    
                    // Send JSON response
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Pesanan berhasil dibuat',
                        'redirect' => BASEURL . '/order/antrian/' . $result['nomor_antrian']
                    ]);
                    exit;
                } else {
                    throw new Exception('Checkout Gagal: ' . $result['message']);
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