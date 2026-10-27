<div align="center">
  <h1>🛒 PHP MySQL Sales Web Application</h1>
  <p>A full-featured e-commerce web application built with native PHP, MySQLi, Docker, and Bootstrap 5.</p>
</div>

---

## 🚀 Giới thiệu (Overview)
Dự án được xây dựng như một nền tảng thương mại điện tử hoàn chỉnh, hỗ trợ đầy đủ các tính năng từ quản lý danh mục, sản phẩm, giỏ hàng session, xác thực tài khoản khách hàng, xử lý giao dịch đơn hàng an toàn bằng Transaction đến tích hợp REST API và Fetch API động.

---

## ✨ Tính năng chính (Key Features)

### 🛍️ Frontend (Khách hàng)
- **Danh mục & Tìm kiếm:** Tìm kiếm sản phẩm theo tên/mã, lọc theo danh mục và phân trang linh hoạt.
- **Chi tiết sản phẩm:** Xem thông tin chi tiết, hình ảnh và kiểm tra số lượng tồn kho theo thời gian thực.
- **Giỏ hàng (Session-based):** Thêm, cập nhật số lượng (giới hạn theo tồn kho), xóa sản phẩm và tự động đồng bộ số lượng trên thanh điều hướng (Navbar).
- **Xác thực tài khoản:** Đăng ký tài khoản (băm mật khẩu an toàn với `password_hash`), đăng nhập/đăng xuất giữ nguyên giỏ hàng.
- **Thanh toán & Đặt hàng (Checkout):** 
  - Hỗ trợ cả khách vãng lai và khách đã đăng nhập (tái sử dụng `CustomerID`).
  - Sử dụng **MySQL Transaction** và khóa `FOR UPDATE` để đảm bảo tính toàn vẹn tồn kho, ngăn chặn xung đột dữ liệu.
- **REST API & Fetch API:** Tải danh mục và sản phẩm động bằng JavaScript `fetch()` mà không cần tải lại toàn bộ trang.

### ⚙️ Admin (Quản trị viên)
- **Quản lý danh mục & sản phẩm:** Thêm, sửa, xóa và quản lý hình ảnh sản phẩm trực quan.
- **Quản lý đơn hàng:** Xem danh sách, chi tiết đơn hàng, theo dõi vòng đời đơn hàng (`Pending` ➔ `Confirmed` ➔ `Shipping` ➔ `Completed` ➔ `Cancelled`).
- **Hoàn trả tồn kho tự động:** Khi đơn hàng chuyển sang trạng thái `Cancelled`, hệ thống tự động hoàn lại số lượng tồn kho thông qua transaction an toàn.

---

## 🛠️ Công nghệ sử dụng (Tech Stack)
- **Backend:** Native PHP (PHP 8+) với MySQLi Prepared Statements.
- **Database:** MySQL 8.0 (chạy trên Docker Container).
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript (Fetch API, DOM manipulation).
- **Environment:** Docker & Docker Compose (Hỗ trợ cấu hình múi giờ `Asia/Ho_Chi_Minh`).

---

## 📂 Cấu trúc thư mục (Project Structure)
```text
php-mysql-sales-template/
│
├── database/            # Schema và dữ liệu mẫu SQL
├── docker/              # Cấu hình môi trường Docker
├── public/              # Thư mục gốc chứa các file truy cập web
│   ├── api/             # Các REST API endpoints trả về JSON
│   ├── admin/           # Khu vực quản trị hệ thống (Quản lý sản phẩm, đơn hàng)
│   └── uploads/         # Hình ảnh sản phẩm tải lên
├── src/                 # Các file cấu hình và thành phần dùng chung (Header, Footer, Navbar, Session, Database)
├── compose.yaml         # Cấu hình Docker Compose
└── README.md
