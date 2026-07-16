# <div align="center">

# 📚 LabTrack

### *Modern Laboratory Asset & Inventory Management System*

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-red?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge">
</p>

<p align="center">
  Laboratory inventory management built with Laravel to simplify asset tracking, borrowing, returns, and inventory administration.
</p>

</div>

---

## ✨ Overview

**LabTrack** is a web-based laboratory management system developed to streamline laboratory administration. The application provides an organized way to manage assets, monitor borrowing activities, record returns, and maintain inventory efficiently.

Designed with scalability and maintainability in mind, LabTrack leverages Laravel's modern architecture for a clean and reliable development experience.

---

# 🚀 Features

* 📦 Asset Management
* 🏷️ Category Management
* 👥 User Management
* 🔐 Authentication & Authorization
* 📥 Borrowing Management
* 📤 Return Management
* 📊 Dashboard & Statistics
* 🔍 Search & Filtering
* 📄 Activity Logging
* 📱 Responsive Interface

---

# 🛠️ Tech Stack

| Technology | Description          |
| ---------- | -------------------- |
| Laravel 11 | Backend Framework    |
| PHP 8.3+   | Programming Language |
| MySQL      | Database             |
| Blade      | Templating Engine    |
| Bootstrap  | Frontend UI          |
| Composer   | Dependency Manager   |
| Docker     | Containerization     |
| Coolify    | Deployment Platform  |

---

# 📂 Project Structure

```text
LabTrack/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
└── vendor/
```

---

# ⚙️ Installation

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/labtrack.git

cd labtrack
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Copy Environment

```bash
cp .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Configure Database

Edit your `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=labtrack
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Run Migration

```bash
php artisan migrate
```

### 7. Start Development Server

```bash
php artisan serve
```

---

# 🐳 Docker

```bash
docker compose up -d --build
```

---

# 🌐 Deployment

LabTrack can be deployed using:

* Docker
* Coolify
* VPS Ubuntu
* Nginx
* Apache

---

# 📖 Screenshots

> Add screenshots here after the UI is complete.

```
docs/
├── dashboard.png
├── assets.png
├── borrow.png
└── users.png
```

---

# 📈 Roadmap

* [ ] Asset QR Code
* [ ] Barcode Scanner
* [ ] Export PDF
* [ ] Export Excel
* [ ] Email Notifications
* [ ] Borrow Approval Workflow
* [ ] Audit Log
* [ ] REST API
* [ ] Mobile Friendly UI
* [ ] Dark Mode

---

# 🤝 Contributing

Contributions are welcome.

1. Fork the repository
2. Create your feature branch

```bash
git checkout -b feature/new-feature
```

3. Commit your changes

```bash
git commit -m "Add new feature"
```

4. Push to branch

```bash
git push origin feature/new-feature
```

5. Open a Pull Request

---

# 📄 License

This project is licensed under the **MIT License**.

---

<div align="center">

### Built with ❤️ using Laravel

**LabTrack** — Laboratory Asset & Inventory Management System

</div>
