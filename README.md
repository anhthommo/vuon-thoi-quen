# 🌻 Vườn Thói Quen — Phiên bản Hợp nhất (Web + PWA)

## ✨ Tính năng giao diện
- Hỗ trợ **Dark mode** trong phần Cài đặt, tự lưu lựa chọn trên thiết bị.
- Hỗ trợ chuyển **Tiếng Việt / English** để người nước ngoài dùng app dễ hơn.

## ✅ Tính năng đồng bộ dữ liệu
Đăng nhập cùng email → dữ liệu có ngay trên mọi thiết bị:
- Mở trên máy tính (web)          → có dữ liệu ✅
- Mở trên điện thoại Android      → có dữ liệu ✅
- Cài app PWA trên iPhone         → có dữ liệu ✅
- Đổi điện thoại mới              → đăng nhập lại → có ngay ✅
- Mất máy / xóa app               → đăng nhập lại → khôi phục ✅

## Cấu trúc file
```
index.html              ← App chính
manifest.json           ← Cấu hình PWA (cài app)
sw.js                   ← Service Worker (offline)
firestore.rules         ← Bảo mật Firebase
icons/                  ← Icon app (5 kích thước)
backup-api/
  backup.php            ← API backup sang MySQL
  schema.sql            ← Tạo bảng MySQL
README.md
```

---

## BƯỚC 1 — Firebase (bắt buộc)

1. https://console.firebase.google.com → Add project
2. Authentication → Get started → bật **Email/Password** + **Google**
3. Firestore Database → Create database → production mode → **asia-southeast1**
4. Firestore → Rules → paste nội dung file `firestore.rules` → Publish
5. Project Settings ⚙️ → Your apps → </> Web → Register → copy config

Trong `index.html` tìm `const FB = {` và paste 6 giá trị vào.

---

## BƯỚC 2 — OneSignal (khuyến nghị cho thông báo iOS/Android)

1. https://onesignal.com → Add App → Web Push → nhập URL website
2. Tải file **OneSignalSDKWorker.js** từ OneSignal → upload vào root hosting
3. Lấy App ID → trong `index.html` thay `YOUR_ONESIGNAL_APP_ID`

> Nếu bỏ qua bước này, thông báo vẫn hoạt động trên trình duyệt desktop khi mở app.

---

## BƯỚC 3 — MySQL Backup (tùy chọn thêm bảo mật)

1. phpMyAdmin → chạy file `backup-api/schema.sql`
2. Trong `backup-api/backup.php` điền: DB_HOST, DB_NAME, DB_USER, DB_PASS, API_SECRET
3. Upload thư mục `backup-api/` lên hosting
4. Trong `index.html` tìm `const MYSQL_URL` và `MYSQL_KEY` → điền vào

---

## BƯỚC 4 — Upload lên hosting

**Dùng host có cPanel (Hostinger, Namecheap...):**
- Upload tất cả file vào `public_html/`
- Authentication → Authorized domains → thêm domain của bạn

**Dùng GitHub Pages (miễn phí):**
```
1. Tạo repo GitHub (Public)
2. Upload tất cả file vào repo
3. Settings → Pages → Deploy from main
4. Link: https://username.github.io/repo-name/
```
> ⚠️ GitHub Pages không chạy được PHP → MySQL backup không hoạt động
> Nhưng Firebase Firestore vẫn hoạt động bình thường ✅

---

## BƯỚC 5 — Cài app trên điện thoại

**Android (Chrome):**
Banner "Cài ngay" tự hiện khi mở web → nhấn để cài ✅

**iPhone (Safari — BẮT BUỘC):**
1. Mở link trong Safari
2. Nút Chia sẻ 📤 → "Thêm vào Màn hình chính" → Thêm ✅

---

## Test nhanh sau khi deploy
1. Mở website → màn hình đăng nhập/đăng ký hiện ✅
2. Đăng ký tài khoản → vào app ✅
3. Thêm thói quen → tick → hoa nở ✅
4. Mở tab mới (hoặc thiết bị khác) → đăng nhập lại → dữ liệu còn ✅
5. Cài đặt → bật thông báo → nhận được push notification ✅
