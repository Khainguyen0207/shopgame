Link web demo:

**Quản lý truy cập**

1. **Quản lý lượng người dùng truy cập bao nhiêu trong ngày thông qua ( IP và Agent )**: Mục đích giúp cho người quản trị biết được Maketting có hiệu quả hay không ( chỉ lấy những truy cập lần đầu trong ngày )
2. Vào 23h59 mỗi ngày bot sẽ gửi 1 file data đến telegram người quản trị và xóa file đó đi sau khi gửi thành công.
****

**Tích hợp bot Telegram**

1. Giúp bắt các lỗi từ web giúp developer dễ dàng debug.
2. Ngoài ra còn giúp gửi các file tiện ích quản lý hằng ngày.

*Ảnh minh họa*
<img src="public/assets/images/demo/image.png" alt="bot-telegram>

**Authentication: Đăng nhập, đăng ký, quên mật khẩu**

- **Đăng nhập**: Login bằng tk mk đã đăng ký trước đó hoặc đăng nhập bằng tài khoản Google.
- **Quên mật khẩu**: Khi nhấn quên mật khẩu hệ thống sẽ check mail tồn tại hay không sau đó gửi email đến người dùng.
- Nội dung mail: Đường link đưa người dùng đến trang đổi mật khẩu - Link chỉ khả dụng trong 60p.

**Admin Panel**

CRUD các mục như: Danh mục, Tài khoản, Dịch vụ, Vòng quay, Ngân hàng.

**Tích hợp Cloudinary SDK**

Với số lượng ảnh khổng lồ thì hosting không thể nào mà chứa hết được vì vậy **Cloudinary SDK** là lựa chọn để giảm tải hosting.

Qui trình admin upload ảnh → Hệ thống sẽ lưu ảnh tạm thời trong **Storage** đồng thời khởi tạo **`public_id`** cho tất cả ảnh sau đó đẩy tất cả vào job → Job sẽ có nhiệm vụ upload lên **Cloudinary →** Cuối cùng là xóa ảnh trong Storage và callback api của **Cloudinary** để nhận link ảnh public thông qua **`public_id.`**

**Tích hợp SePay cho thanh toán :**

Setup thông qua admin panel.

Khi người dùng quét mã QR tại trang profile và thanh toán thành công: Sepay sẽ bắn 1 webhook tới web → Web nhận response và trả về `success: true` `code:200` để xác nhận thành công, và truyền vào Jobs (Laravel) để thay đổi số tiền của người dùng thông qua ghi chú của người dùng mà admin đã setup sẵn.

*Ảnh minh họa*

<img src="public/assets/images/demo/banking.png" alt="banking">
<img src="public/assets/images/demo/confirm_banking.png" alt="confirm_banking">
