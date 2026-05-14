# 🚀 OpsMind Suite

**OpsMind Suite** is a modern, high-performance web application designed for comprehensive financial management and business operations. Built with a focus on speed, scalability, and multi-tenant isolation, it provides a seamless experience for managing transactions, invoices, and accounting tasks.

---

## ✨ Key Features

- **🏛️ Multi-Tenancy**: Complete data isolation using a robust `TenantScope` system.
- **💰 Financial Management**: Full accounting suite with Chart of Accounts (CoA) and Journal Entries.
- **📄 Invoicing System**: Professional invoice generation, tracking, and management.
- **📊 Real-time Dashboard**: Interactive KPI cards, monthly revenue charts, and transaction summaries.
- **🤖 AI Integration**: Built-in AI chatbot support for business assistance.
- **🔒 Security**: OAuth 2.0 (GitHub) integration, OTP verification, and role-based access control.
- **📱 Responsive UI**: Beautifully crafted with Tailwind CSS and Vue 3 (Inertia.js).

---

## 🛠️ Technology Stack

| Role | Technology |
| :--- | :--- |
| **Backend** | [Laravel 11+](https://laravel.com/) |
| **Frontend** | [Vue.js 3](https://vuejs.org/) (Composition API) |
| **SPA Framework** | [Inertia.js](https://inertiajs.com/) |
| **Database** | MySQL / PostgreSQL |
| **Caching** | [Redis](https://redis.io/) |
| **Styling** | [Tailwind CSS](https://tailwindcss.com/) |
| **Charts** | [Chart.js](https://www.chartjs.org/) |

---

## ⚡ Performance Optimizations

OpsMind is engineered for speed. Recent optimizations include:

### 1. Redis Caching Strategy
We use a **Tenant-Aware caching** mechanism. Dashboard metrics, user profiles, and notifications are stored in Redis with per-tenant keys (`tenant_{id}:...`).
- **Auto-Invalidation**: Using Laravel Observers, the cache is automatically cleared when relevant models (`Transaction`, `Invoice`, `Account`) are modified.

### 2. Instant Load Architecture
By moving heavy computations to Redis and removing asynchronous `defer` dependencies, we achieved an **instant initial render**. Essential dashboard data is served directly from the cache during the initial request.

### 3. Progressive Loading
For heavier sections like the Subscription Plan, we utilize **Inertia.js Deferred Props** combined with a custom **Glassmorphism Skeleton UI** to ensure the app feels responsive even during cold starts.

---

## 📦 Installation

To get started with OpsMind locally, follow these steps:

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- Redis Server

### Setup
1. **Clone the repository**
   ```bash
   git clone https://github.com/Prawira123/OpsMind-App.git
   cd OpsMind-App
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database & Redis**
   Configure your database and Redis credentials in `.env`.

5. **Run Migrations & Seeds**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the Application**
   ```bash
   php artisan serve
   npm run dev
   ```

---

## 🤝 Contributing

We welcome contributions! Please feel free to submit Pull Requests or report issues via the GitHub Issues tab.

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---
<p align="center">Made with ❤️ for modern business operations.</p>
