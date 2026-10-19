<?php
/**
 * HomeController - Handles home page, contact
 * Include model trước, sau đó include header -> view content -> footer
 */
class HomeController {
    public function trangChu() {
        $tieuDeTrang = 'VAB - Thời Trang Thể Thao';
        $trangHienTai = 'home';
        $nhungTuController = true;
        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();
        $sanPhamMoi = $productModel->getAll();
        include './views/giaodien/dautrang.php';
        include './views/giaodien/index.php';
        include './views/giaodien/chantrang.php';
    }
}