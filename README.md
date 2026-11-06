<div align="center">

# 📢 Smart Digital Notice Board Management System

### A Priority-Aware Notice Platform with Dynamic Scheduling and AI-Based Summarization

*Undergraduate thesis project — Department of Computer Science & Engineering, KUET*

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Flutter](https://img.shields.io/badge/Flutter-Android-02569B?logo=flutter&logoColor=white)](https://flutter.dev)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Neon-4169E1?logo=postgresql&logoColor=white)](https://neon.tech)
[![Docker](https://img.shields.io/badge/Docker-Deployed-2496ED?logo=docker&logoColor=white)](https://www.docker.com)
[![Gemini](https://img.shields.io/badge/Google-Gemini%20AI-4285F4?logo=google&logoColor=white)](https://ai.google.dev)
[![Firebase](https://img.shields.io/badge/Firebase-FCM-FFCA28?logo=firebase&logoColor=black)](https://firebase.google.com)

**[🌐 Live Demo](https://notice-board-jowu.onrender.com)** &nbsp;•&nbsp; **[📺 Display Board](https://notice-board-jowu.onrender.com/board/1)** &nbsp;•&nbsp; **[📖 User Manual](#-documentation)**

<!-- Replace with a banner screenshot or a short GIF of the display board -->
<!-- ![Banner](docs/screenshots/banner.png) -->

</div>

> **Note:** The live demo runs on a free hosting tier, so the first request after a period of inactivity may take 30–50 seconds while the server wakes up.

---

## 🎯 The Problem

Paper notice boards in academic departments have three fundamental weaknesses:

1. **Presence-dependent** — a student who is not physically in front of the board misses the notice.
2. **No priority** — an urgent exam notice competes equally with a routine announcement.
3. **No record or reach** — old notices are removed, and nothing reaches students remotely.

Moving to a digital screen creates a new problem: a screen can show only one notice at a time, so **which notice should be shown, when, and how often?**

## 💡 The Solution

One centralized backend serves **three connected clients**, so a notice created once reaches everyone instantly.

| Client | Used by | Purpose |
|---|---|---|
| 🖥️ **Web Dashboard** | Admin, Teachers | Manage notices, users, courses, events |
| 📱 **Android App** | Students, CRs, Teachers | Read notices, post class updates, reply, receive push alerts |
| 📺 **Display Board** | Everyone in the department | Live full-screen board, driven by a custom scheduling algorithm |

The core contribution is a **priority-aware dynamic scheduling algorithm** that decides what the board shows, backed by an **AI summarizer** that condenses long PDF notices into short readable summaries.

---

## ✨ Key Features

### 🧠 Priority-Aware Dynamic Scheduling *(research contribution)*
Every notice receives a real-time score blending four factors, recomputed on each board refresh:

```
Score = 0.4·Priority + 0.3·Urgency + 0.2·Freshness + 0.1·Fairness
```

| Factor | Definition | Purpose |
|---|---|---|
| **Priority** | high = 1.0, medium = 0.6, low = 0.3 | Important notices appear more |
| **Urgency** | `1 / (daysToDeadline + 1)` | Deadlines approaching rise in rank |
| **Freshness** | `1 / (daysSincePosted + 1)` | New notices get attention |
| **Fairness** | `1 / (timesShown + 1)` | **Prevents starvation** — no notice is ignored |

An **emergency override** instantly takes over the whole board when an emergency notice is active.

### 🤖 AI-Powered PDF Summarization
Uploaded PDFs are parsed and summarized with **Google Gemini**, so students grasp key details (date, venue, deadline) at a glance.

### 👥 Role-Based Access Control
Four roles — **Admin, Teacher, Class Representative (CR), Student** — each with a tailored interface, enforced at both route and controller level.

### 📚 Course-Aware CR → Teacher Workflow
CRs select a course from **their own academic year**; the system then lists only that course's teachers. Teachers reply directly, and the CR sees the reply in-app.

### 🔔 Real Push Notifications
Firebase Cloud Messaging (**HTTP v1 API**) alerts users when notices are published, when a CR contacts a teacher, and when a teacher replies.

### 🎓 Academic Session Management
Students register with year and section. Admins can **bulk-promote** a whole batch in one action, update individuals, and remove graduating students.

### 📺 Rich Display Board
Priority-scheduled main notice • event image carousel • **CR updates and teacher notices side by side** • configurable live ticker • QR code for mobile access • emergency mode.

### 🔐 Security
Bcrypt password hashing • Sanctum token auth • **email verification** • admin approval workflow • CSRF protection • signed verification links • audit logging • secrets via environment variables.

### 📱 Public QR Access
Scan the board's QR code to open a mobile-friendly public page with all current notices and PDFs — no login required.

---

## 📊 Scheduling Algorithm — Results

Evaluated by simulating **100 display slots over 12 notices** against two baselines:

| Method | High-Priority Exposure | Urgent Exposure | Fairness (Jain) | Min Shown |
|---|:---:|:---:|:---:|:---:|
| Round-Robin | 27% | 27% | 0.997 | 8 |
| Static Priority | 60% | 48% | 0.576 | 2 |
| **Dynamic (Proposed)** | **54%** | **48%** | **0.613** | **5** |

- **Round-Robin** is perfectly fair but blind to importance.
- **Static Priority** favors important notices but starves minor ones (only 2 slots).
- **Dynamic** balances all four objectives — high exposure for important notices *and* no starvation (min 5 slots).

Reproduce the results:
```bash
php artisan algo:compare
```

---

## 🏗️ Architecture

```mermaid
flowchart TB
    subgraph Users
        A[Admin]; T[Teacher]; C[CR]; S[Student]
    end

    subgraph Clients
        W[Web Dashboard<br/>Laravel + AdminLTE]
        M[Mobile App<br/>Flutter]
        B[Display Board<br/>Kiosk / Electron]
    end

    subgraph Backend[Laravel Backend - REST API]
        N[Notice Manager]
        SC[Scheduling Engine]
        AI[AI Summarizer]
        F[FCM Service]
    end

    subgraph Services
        DB[(Neon PostgreSQL)]
        CL[Cloudinary<br/>Images and PDFs]
        G[Google Gemini]
        FB[Firebase FCM]
    end

    A & T --> W
    T & C & S --> M
    S --> B
    W & M & B --> Backend
    N --> DB
    N --> CL
    SC --> DB
    AI --> G
    F --> FB
```

### Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.3, Laravel Sanctum |
| Web UI | Blade, AdminLTE, Bootstrap |
| Mobile | Flutter (Dart), Firebase Messaging |
| Database | PostgreSQL (Neon) / MySQL (local) |
| File Storage | Cloudinary |
| AI | Google Gemini API |
| Push | Firebase Cloud Messaging (HTTP v1) |
| Display | Chrome kiosk / Electron player |
| Deployment | Docker, Nginx, PHP-FPM, Render |

### Database Schema

```mermaid
erDiagram
    USERS ||--o{ NOTICES : authors
    USERS ||--o{ NOTICES : "notified as teacher"
    USERS ||--o{ EVENTS : creates
    USERS ||--o{ AUDIT_LOGS : performs
    USERS ||--o{ TICKER_MESSAGES : publishes
    COURSES ||--o{ NOTICES : "belongs to"
    COURSES }o--o{ USERS : "taught by"
    NOTICES ||--o{ NOTICE_VIEWS : receives
```

---

## 📸 Screenshots

<!-- Replace each placeholder with an actual screenshot in docs/screenshots/ -->

| Display Board | Admin Dashboard |
|:---:|:---:|
| ![Board](docs/screenshots/board.png) | ![Admin](docs/screenshots/admin.png) |

| Student App | CR Post Screen | Teacher App |
|:---:|:---:|:---:|
| ![Student](docs/screenshots/student_app.png) | ![CR](docs/screenshots/cr_post.png) | ![Teacher](docs/screenshots/teacher_app.png) |

---

## 🚀 Getting Started

### Prerequisites
PHP 8.3+ • Composer • MySQL or PostgreSQL • Node.js • Flutter SDK (for the app)

### 1. Backend

```bash
git clone https://github.com/rifat9754/Smart_Notice_Board.git
cd Smart_Notice_Board

composer install
cp .env.example .env
php artisan key:generate
```

Configure `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=notice_board
DB_USERNAME=root
DB_PASSWORD=your_password

GEMINI_API_KEY=your_gemini_key
CLOUDINARY_URL=cloudinary://key:secret@cloud_name
FIREBASE_PROJECT_ID=your_firebase_project
FIREBASE_CREDENTIALS=storage/app/firebase/service-account.json
```

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open `http://127.0.0.1:8000` — the display board is at `/board/1`.

### 2. Run on a local network (same WiFi)

```bash
php artisan serve --host=0.0.0.0 --port=8000
```
Set `APP_URL=http://<your-ip>:8000` in `.env`, then open that address from any device on the same network.

### 3. Mobile App

```bash
cd mobile            # Flutter project directory
flutter pub get
# Set baseUrl in lib/api.dart, add google-services.json to android/app/
flutter run
```

### 4. Display Board Player (Electron)

```bash
cd notice-tv-player
npm install
npm start
```
The player opens the board on the **external display** and a control panel on the primary screen.

### Docker

```bash
docker build -t notice-board .
docker run -p 8080:80 --env-file .env notice-board
```

---

## 📂 Project Structure

```
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/DisplayController.php     # ⭐ Scheduling algorithm
│   │   ├── Api/NoticeApiController.php   # Mobile API
│   │   ├── NoticeController.php          # Notices + AI summary
│   │   ├── CourseController.php          # Course management
│   │   └── PromotionController.php       # Academic year promotion
│   ├── Services/
│   │   ├── AiSummaryService.php          # Gemini integration
│   │   └── FcmService.php                # Push notifications
│   └── Console/Commands/
│       └── CompareScheduling.php         # Algorithm simulation
├── resources/views/                      # Dashboard, board, public pages
├── routes/ (web.php, api.php)
├── database/migrations/
├── docker/                               # Nginx config
└── Dockerfile
```

---

## 🔒 Security

- Passwords hashed with bcrypt; API access through Sanctum tokens
- Mandatory email verification, then admin approval before first login
- Route- and controller-level role enforcement (e.g. only admins edit official notices)
- File upload validation and size limits; CSRF protection on all forms
- Secrets kept in environment variables, never committed

> **Known limitations:** no 2FA yet; board API endpoints are intentionally public (required by the display); Android only.

---

## 🗺️ Roadmap

- [ ] iOS application
- [ ] Google Play Store release
- [ ] Multi-department / university-wide support
- [ ] Two-factor authentication
- [ ] Long-term field study to refine scheduling weights

---

## 📖 Documentation

- 📘 **Thesis report** — full methodology, algorithm derivation, and evaluation *(add link)*
- 📗 **User manual** — step-by-step guide for every role *(add link)*

---

## 🎓 About

Built as an undergraduate thesis at the **Department of Computer Science & Engineering, Khulna University of Engineering & Technology (KUET)**, under the supervision of **Prof. Dr. Sk. Imran Hossain**.

**Author:** Md. Ahanaf Tahmid
[![GitHub](https://img.shields.io/badge/GitHub-rifat9754-181717?logo=github)](https://github.com/rifat9754)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-Connect-0A66C2?logo=linkedin)](https://www.linkedin.com/in/ahanaf-tahmid-rifat-220663345)

---

<div align="center">

**Innovation in Every Notice** ⭐ *If you find this project interesting, consider giving it a star.*

</div>
