# 🧠 Byteforce Quiz Platform  
**Development of an online quiz platform with real-time results and integrated eye-tracking to monitor user attention**

---

## 🚀 Project Overview
The **Byteforce Quiz Platform** is a web-based application that allows users to take quizzes in real time while using **eye-tracking technology** to monitor attention and detect potential foul play.  
This system ensures **fairness**, **instant feedback**, and **seamless management** for both administrators and participants.

---

## 🧩 Key Features
- 🧾 **User Authentication** — Secure login and registration for participants and admins.  
- 🎯 **Eye Tracking Integration** — Uses the browser’s webcam with WebGazer.js to monitor user focus and detect distractions.  
- ⚡ **Real-Time Results** — Immediate score calculation and leaderboard updates after submission.  
- 🧑‍💼 **Admin Dashboard** — Manage quizzes, questions, users, and leaderboard data with full CRUD operations.  
- 📊 **Leaderboard** — Displays top performers dynamically.  
- 🗄️ **Database-Driven** — MySQL database handles persistent data storage.  

---

## 🛠️ Tech Stack
**Frontend:** HTML, CSS, JavaScript (WebGazer.js, AJAX)  
**Backend:** PHP (Procedural + OOP)  
**Database:** MySQL  
**Hosting Environment:** Localhost / Any Web Hosting Platform  

---

## 🧱 Project Structure
```
htdocs/
│
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── app.js
│       ├── leaderboard.js
│       ├── quiz_app.js
│       └── webgazer-wrapper.js
│
├── db/
│   └── schema.sql
│
└── php/
    ├── add_question.php
    ├── admin_dashboard.php
    ├── config.sample.php  ← Example config file (safe)
    ├── DB.php
    ├── login.php
    ├── QuizManager.php
    ├── register.php
    ├── quiz.php
    ├── results.php
    └── ...
```

---

## ⚙️ Setup Instructions

### Local Development (XAMPP)
1. Clone the repository:  
   ```bash
   git clone https://github.com/Vijaya-krishna/Byteforce_Quiz_Platform.git
   ```
2. Move the folder into your `htdocs` directory.  
3. Import the SQL file:
   - Open **phpMyAdmin** → Create a new database (e.g., `quiz_platform`)  
   - Import `db/schema.sql`
4. Copy `htdocs/php/config.sample.php` → rename it to `config.php`  
   Fill in your local credentials:
   ```php
   <?php
   class Config {
       public $host = "localhost";
       public $user = "root";
       public $pass = "";
       public $db   = "quiz_platform";
   }
   ?>
   ```
5. Start Apache and MySQL from XAMPP Control Panel.  
6. Visit [http://localhost/Byteforce_Quiz_Platform/htdocs/php/login.php](http://localhost/Byteforce_Quiz_Platform/htdocs/php/login.php)

---

## 🌐 Deployment (Web Hosting Platform)
1. Upload all files inside the `htdocs` folder to your **web hosting platform’s `/htdocs/` or public_html directory**.  
2. Update your `config.php` with the web host database credentials:
   ```php
   <?php
   class Config {
       public $host = "YOUR_HOST";
       public $user = "YOUR_USERNAME";
       public $pass = "YOUR_PASSWORD";
       public $db   = "YOUR_DATABASE";
   }
   ?>
   ```
3. Import `db/schema.sql` into your hosting control panel’s **phpMyAdmin**.  
4. Access your hosted URL to use the live quiz platform.

---

## 🔐 Security Notes
- Never commit real credentials (`config.php`) to GitHub.  
- Always use `config.sample.php` for demonstration.  
- Use HTTPS hosting for secure webcam and data transmission.  

---

## 👨‍💻 Contributors
- **Vijaya Krishna** — Project Lead, Backend & Integration  
- **Team Byteforce** — UI, Eye Tracking, Testing, Documentation  

---

## 🧾 Project Statement
> “Development of an online quiz platform with real-time results and integrated eye-tracking to monitor user attention.”

---

## 🧠 Future Enhancements
- AI-based gaze anomaly detection for advanced cheating prevention.  
- Integration of face recognition to verify user identity.  
- Responsive mobile layout and multilingual support.  

---

## 📜 License
This project is open-source and available under the [MIT License](LICENSE).
