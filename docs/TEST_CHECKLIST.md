# Checklist kiểm thử BookNest

- [ ] Trang chủ hiển thị banner, danh mục và sách.
- [ ] Tìm kiếm theo tên sách/tác giả; phân trang hoạt động.
- [ ] Đăng ký tài khoản đầu tiên trên database trống: `role=admin`.
- [ ] Đăng ký tài khoản thứ hai: `role=customer`.
- [ ] Tài khoản customer truy cập `/admin` bị từ chối/chuyển hướng.
- [ ] Admin thêm, sửa, xóa sách; validation báo lỗi với dữ liệu không hợp lệ.
- [ ] Admin thêm, sửa, xóa danh mục; sách liên kết đúng danh mục.
- [ ] Thêm sách vào giỏ, cập nhật số lượng và xóa sách.
- [ ] Checkout tạo đơn hàng và các dòng chi tiết; tổng tiền đúng.
- [ ] Admin xem/cập nhật trạng thái đơn hàng.
- [ ] Đăng xuất hủy phiên đăng nhập.
- [ ] Ảnh bìa không bị lỗi đường dẫn.
