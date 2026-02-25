# Invoice Management API

Laravel API لإدارة الفواتير والعقود والدفعات.

---

## التشغيل

```bash
composer install
cp .env.example .env
php artisan key:generate
```

اضبط قاعدة البيانات في `.env`، ثم:

```bash
php artisan migrate
php artisan db:seed
php artisan serve
