> Полноценная веб-платформа для поиска работы и найма сотрудников с AI-подбором, созданная специально для решения проблемы неоцифрованного рынка труда в Мангистауской области.

**🏆 Решение для 1 места на хакатоне Mangystau Hackathon 2025**

![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

---

## 📋 Содержание

1. [Обзор проекта](#-обзор-проекта)
2. [Технологический стек](#-технологический-стек)
3. [Структура файлов](#-структура-файлов)
4. [База данных](#-база-данных)
5. [API эндпоинты](#-api-эндпоинты)
6. [AI алгоритм матчинга](#-ai-алгоритм-матчинга)
7. [Установка и настройка](#-установка-и-настройка)
8. [Деплой на хостинг](#-деплой-на-хостинг)
9. [Тестирование](#-тестирование)
10. [Решение проблем](#-решение-проблем)
11. [Безопасность](#-безопасность)
12. [План развития](#-план-развития-roadmap)

---

## 🎯 Обзор проекта

### Проблема
- Вакансии живут в WhatsApp-чатах
- hh.ru не охватывает малый бизнес Актау
- Молодёжь не видит возможностей рядом с домом

### Решение
**Mangystau Jobs** — единая платформа с:
- 🤖 **AI-подбором** вакансий
- 📍 **Фильтрацией по микрорайонам**
- 🔔 **Telegram-уведомлениями** (в плане)
- 💼 **Личным кабинетом** для соискателей и работодателей

---

## 🛠 Технологический стек

| Категория | Технологии |
|-----------|------------|
| **Frontend** | HTML5, CSS3, Tailwind CSS, JavaScript ES6, Font Awesome 6 |
| **Backend** | PHP 8+, PDO, REST API, Sessions |
| **База данных** | MySQL |
| **Сервер** | Apache (XAMPP/WAMP/MAMP) |

---

## 📁 Структура файлов
📁 mangystau_hackathon/
│
├── 📄 index.html # Главная страница (тёмная тема)
├── 📄 features.html # Подробная страница возможностей
├── 📄 documentation.html # Полная документация
├── 📄 register.php # Страница регистрации
├── 📄 login.php # Страница входа
├── 📄 dashboard.php # Личный кабинет (AI-рекомендации)
├── 📄 logout.php # Выход из системы
├── 📄 database.php # Подключение к БД + создание таблиц
├── 📄 database.sql # SQL-дамп для импорта
├── 📄 style.css # Кастомные стили + анимации
├── 📄 script.js # JavaScript (fetch, UI, уведомления)
│
├── 📁 api/
│ ├── 📄 api_get_jobs.php # GET: список вакансий с фильтрацией
│ ├── 📄 api_apply.php # POST: отклик на вакансию
│ ├── 📄 api_ai_match.php # GET: AI-рекомендации для соискателя
│ ├── 📄 api_create_job.php # POST: создание вакансии
│ ├── 📄 api_get_my_jobs.php # GET: вакансии работодателя
│ ├── 📄 api_get_responses.php # GET: отклики на вакансии
│ └── 📄 api_update_status.php # POST: обновление статуса отклика
│
└── 📁 assets/
└── 📁 images/ # Изображения для сайта

text

**Всего файлов:** 19 | **Общий размер:** ~150KB

---

## 🗄 База данных

### Таблица: `users`

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | INT PRIMARY KEY AUTO_INCREMENT | Уникальный ID пользователя |
| `email` | VARCHAR(255) UNIQUE | Email для входа |
| `password` | VARCHAR(255) | Хеш пароля (bcrypt) |
| `fullname` | VARCHAR(255) | Полное имя пользователя |
| `phone` | VARCHAR(50) | Номер телефона |
| `role` | ENUM('seeker','employer') | Роль: соискатель или работодатель |
| `microdistrict` | VARCHAR(50) | Микрорайон проживания/работы |
| `skills` | TEXT | Навыки (через запятую) |
| `experience` | INT | Опыт работы в годах |
| `created_at` | TIMESTAMP | Дата регистрации |

### Таблица: `jobs`

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | INT PRIMARY KEY | Уникальный ID вакансии |
| `employer_id` | INT FOREIGN KEY | ID работодателя |
| `title` | VARCHAR(255) | Название вакансии |
| `description` | TEXT | Описание вакансии |
| `skills_required` | TEXT | Требуемые навыки |
| `salary_min` | INT | Минимальная зарплата |
| `salary_max` | INT | Максимальная зарплата |
| `microdistrict` | VARCHAR(50) | Микрорайон работы |
| `type` | ENUM('full','part','freelance') | Тип занятости |
| `is_active` | BOOLEAN | Активна ли вакансия |

### Таблица: `applications`

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | INT PRIMARY KEY | Уникальный ID отклика |
| `job_id` | INT FOREIGN KEY | ID вакансии |
| `seeker_id` | INT FOREIGN KEY | ID соискателя |
| `status` | ENUM('pending','accepted','rejected') | Статус отклика |
| `ai_score` | INT | AI балл совпадения (0-100) |
| `created_at` | TIMESTAMP | Дата отклика |

### SQL код для создания таблиц

```sql
-- Создание базы данных
CREATE DATABASE IF NOT EXISTS mangystau_jobs;
USE mangystau_jobs;

-- Таблица users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    role ENUM('seeker', 'employer') DEFAULT 'seeker',
    microdistrict VARCHAR(50),
    skills TEXT,
    experience INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица jobs
CREATE TABLE jobs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    skills_required TEXT,
    salary_min INT,
    salary_max INT,
    microdistrict VARCHAR(50),
    type ENUM('full', 'part', 'freelance') DEFAULT 'full',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Таблица applications
CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    seeker_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    ai_score INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Тестовые данные
INSERT INTO users (email, password, fullname, role, microdistrict, skills, experience) VALUES
('seeker@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Тестовый Соискатель', 'seeker', '3', 'бариста, кассир, английский', 2),
('employer@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Тестовый Работодатель', 'employer', '5', '', 0);

INSERT INTO jobs (employer_id, title, description, skills_required, salary_min, salary_max, microdistrict, type) VALUES
(2, 'Бариста', 'Поиск опытного бариста', 'бариста, кофемашина', 150000, 200000, '3', 'full');
🔗 Связи между файлами

Навигационные ссылки

index.html → login.php, register.php
index.html → features.html, documentation.html
register.php (успех) → dashboard.php
login.php (успех) → dashboard.php
dashboard.php → logout.php
logout.php → index.html
API вызовы (fetch)

dashboard.php → api_get_jobs.php
dashboard.php → api_ai_match.php
dashboard.php → api_apply.php
dashboard.php → api_create_job.php
dashboard.php → api_get_my_jobs.php
dashboard.php → api_get_responses.php
Все PHP файлы включают database.php для подключения к БД.

📡 API эндпоинты

GET /api/api_get_jobs.php

Получение списка вакансий с фильтрацией.

Параметры:

title (string) - поиск по названию/описанию
district (string) - фильтр по микрорайону
type (string) - фильтр по типу занятости
Ответ: JSON массив объектов вакансий

json
[
  {
    "id": 1,
    "title": "Бариста",
    "description": "Поиск опытного бариста",
    "salary_min": 150000,
    "salary_max": 200000,
    "microdistrict": "3",
    "type": "full"
  }
]
POST /api/api_apply.php

Создание отклика на вакансию.

Параметры: job_id (int)

Ответ: {"success": true, "ai_score": 94}

GET /api/api_ai_match.php

Получение AI-рекомендаций для соискателя.

Ответ: JSON массив вакансий с процентом совпадения

POST /api/api_create_job.php

Создание новой вакансии (только для работодателя).

Параметры: title, description, skills, salary_min, salary_max, district, type

Ответ: {"success": true}

GET /api/api_get_my_jobs.php

Получение вакансий работодателя.

Ответ: JSON массив вакансий с количеством откликов

GET /api/api_get_responses.php

Получение откликов на вакансии работодателя.

Ответ: JSON массив откликов с данными соискателей

POST /api/api_update_status.php

Обновление статуса отклика.

Параметры: app_id, status

Ответ: {"success": true}

🤖 AI алгоритм матчинга

Формула расчёта совпадения

text
Match Score = (Skills Match × 60%) + (Location Match × 30%) + (Experience Match × 10%)
Компоненты расчёта

Компонент	Вес	Описание
Навыки	60%	Сравнение навыков соискателя с требованиями вакансии через пересечение множеств
Локация	30%	Совпадение микрорайона даёт +30 баллов, в противном случае +10
Опыт	10%	Чем ближе опыт к требованию, тем выше балл (макс 10)
Пример работы AI

Соискатель:

Навыки: "бариста, кассир, английский"
Микрорайон: 3
Опыт: 2 года
Вакансия:

Навыки: "бариста, кофемашина"
Микрорайон: 3
Требование опыта: 1+ год
Расчёт:

Навыки: общие: ["бариста"] → 1/2 × 60 = 30 баллов
Локация: совпадает → 30 баллов
Опыт: 2 года ≥ 1 года → 10 баллов
ИТОГО: 70% совпадения

PHP код AI матчинга

php
<?php
function calculateAIMatch($seekerSkills, $jobSkills, $seekerDistrict, $jobDistrict, $seekerExp, $jobReqExp) {
    // Навыки (60%)
    $seekerSkillsArray = array_map('trim', explode(',', strtolower($seekerSkills)));
    $jobSkillsArray = array_map('trim', explode(',', strtolower($jobSkills)));
    $commonSkills = array_intersect($seekerSkillsArray, $jobSkillsArray);
    $skillsScore = (count($commonSkills) / max(1, count($jobSkillsArray))) * 60;
    
    // Локация (30%)
    $locationScore = ($seekerDistrict == $jobDistrict) ? 30 : 10;
    
    // Опыт (10%)
    $experienceScore = ($seekerExp >= $jobReqExp) ? 10 : max(0, ($seekerExp / max(1, $jobReqExp)) * 10);
    
    return round($skillsScore + $locationScore + $experienceScore);
}
?>
