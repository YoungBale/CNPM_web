</div><!-- /.admin-content -->
    </div><!-- /.admin-main -->
</div><!-- /.admin-wrapper -->

<!-- Confirm Modal -->
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-modal">
        <div class="confirm-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <h4>Xác nhận</h4>
        <p id="confirmMessage">Bạn có chắc chắn muốn thực hiện hành động này?</p>
        <div class="confirm-buttons">
            <button class="btn-confirm-yes" id="confirmYes">Xác nhận</button>
            <button class="btn-confirm-no" id="confirmNo">Hủy</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast-notification" id="toastNotification">Thành công!</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/admin.js?v=<?= time() ?>"></script>
</body>
</html>