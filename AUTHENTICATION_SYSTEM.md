# نظام المصادقة الكامل - Complete Authentication System

## نظرة عامة - Overview

تم تطوير نظام مصادقة شامل ومتكامل يتضمن جميع الميزات الأساسية للمصادقة والأمان.

A comprehensive and integrated authentication system has been developed that includes all essential authentication and security features.

## الميزات المتاحة - Available Features

### 1. تسجيل المستخدمين - User Registration
- تسجيل مستخدم جديد مع التحقق من صحة البيانات
- دعم كلمات المرور المؤكدة
- إنشاء جلسة مستخدم تلقائياً

### 2. تسجيل الدخول - User Login
- تسجيل دخول بالبريد الإلكتروني وكلمة المرور
- حماية من محاولات تسجيل الدخول الفاشلة
- تتبع جلسات المستخدمين
- دعم تسجيل الدخول بـ OTP

### 3. إدارة الملف الشخصي - Profile Management
- عرض الملف الشخصي
- تحديث المعلومات الشخصية
- تغيير كلمة المرور
- إدارة الجلسات النشطة

### 4. التحقق من البريد الإلكتروني - Email Verification
- إرسال رمز OTP للبريد الإلكتروني
- التحقق من صحة الرمز
- دعم أغراض متعددة (تسجيل، تسجيل دخول، إعادة تعيين كلمة المرور)

### 5. المصادقة الاجتماعية - Social Authentication
- تسجيل الدخول عبر Google
- تسجيل الدخول عبر Facebook
- ربط حسابات اجتماعية متعددة
- إلغاء ربط الحسابات الاجتماعية

### 6. الأمان والحماية - Security & Protection
- حماية من محاولات تسجيل الدخول الفاشلة
- تحديد معدل الطلبات (Rate Limiting)
- تتبع جلسات المستخدمين
- حماية من هجمات Brute Force

## API Endpoints

### Public Routes (لا تحتاج مصادقة)

#### Authentication
```
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
```

#### OTP Management
```
POST /api/auth/send-otp
POST /api/auth/verify-otp
POST /api/auth/resend-otp
```

#### Social Authentication
```
POST /api/auth/google
POST /api/auth/facebook
```

### Protected Routes (تحتاج مصادقة)

#### User Profile
```
GET /api/auth/profile
PUT /api/auth/profile
POST /api/auth/change-password
GET /api/auth/sessions
POST /api/auth/revoke-all-sessions
```

#### Social Account Management
```
POST /api/social/link
DELETE /api/social/unlink
GET /api/social/accounts
```

#### General
```
GET /api/user
```

## أمثلة على الاستخدام - Usage Examples

### 1. تسجيل مستخدم جديد - Register New User

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+966501234567"
  }'
```

### 2. تسجيل الدخول - Login

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmed@example.com",
    "password": "password123"
  }'
```

### 3. إرسال OTP - Send OTP

```bash
curl -X POST http://localhost:8000/api/auth/send-otp \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmed@example.com",
    "purpose": "verify_email"
  }'
```

### 4. التحقق من OTP - Verify OTP

```bash
curl -X POST http://localhost:8000/api/auth/verify-otp \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmed@example.com",
    "code": "123456",
    "purpose": "verify_email"
  }'
```

### 5. تسجيل الدخول الاجتماعي - Social Login

```bash
curl -X POST http://localhost:8000/api/auth/google \
  -H "Content-Type: application/json" \
  -d '{
    "google_id": "google_user_id",
    "email": "user@gmail.com",
    "name": "User Name",
    "avatar": "https://example.com/avatar.jpg"
  }'
```

### 6. تحديث الملف الشخصي - Update Profile

```bash
curl -X PUT http://localhost:8000/api/auth/profile \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "أحمد محمد الجديد",
    "phone": "+966501234568",
    "lat": 24.7136,
    "lang": 46.6753
  }'
```

## قاعدة البيانات - Database Structure

### جدول المستخدمين - Users Table
- `id`: المعرف الفريد
- `name`: الاسم
- `email`: البريد الإلكتروني
- `password`: كلمة المرور (مشفرة)
- `phone`: رقم الهاتف
- `avatar`: رابط الصورة الشخصية
- `is_active`: حالة النشاط
- `is_admin`: حساب إداري
- `lat`, `lang`: الإحداثيات الجغرافية
- `email_verified_at`: تاريخ التحقق من البريد
- `phone_verified_at`: تاريخ التحقق من الهاتف

### جدول الحسابات الاجتماعية - Social Accounts Table
- `id`: المعرف الفريد
- `user_id`: معرف المستخدم
- `provider`: مزود الخدمة (google, facebook)
- `provider_id`: معرف المستخدم في المزود
- `profile`: بيانات الملف الشخصي (JSON)

### جدول أكواد OTP - Email OTPs Table
- `id`: المعرف الفريد
- `email`: البريد الإلكتروني
- `code`: الكود
- `expires_at`: تاريخ انتهاء الصلاحية
- `used`: حالة الاستخدام

### جدول جلسات المستخدمين - User Sessions Table
- `id`: المعرف الفريد
- `user_id`: معرف المستخدم
- `ip_address`: عنوان IP
- `user_agent`: معلومات المتصفح
- `last_activity`: آخر نشاط

### جدول محاولات تسجيل الدخول الفاشلة - Failed Logins Table
- `id`: المعرف الفريد
- `email`: البريد الإلكتروني
- `ip_address`: عنوان IP
- `attempted_at`: تاريخ المحاولة

## الأمان - Security Features

### 1. حماية من Brute Force
- تتبع محاولات تسجيل الدخول الفاشلة
- حظر مؤقت للعنوان IP بعد محاولات متعددة
- حظر مؤقت للبريد الإلكتروني بعد محاولات متعددة

### 2. Rate Limiting
- تحديد معدل الطلبات لكل مستخدم
- حماية من هجمات DDoS
- إضافة headers للمعلومات

### 3. إدارة الجلسات
- تتبع جميع جلسات المستخدم
- إمكانية إلغاء جميع الجلسات
- تنظيف الجلسات القديمة

### 4. تشفير البيانات
- تشفير كلمات المرور باستخدام Hash
- حماية البيانات الحساسة
- استخدام Laravel Sanctum للمصادقة

## التكوين - Configuration

### متغيرات البيئة - Environment Variables

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_store
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1
```

## التشغيل - Running the System

### 1. تثبيت المتطلبات - Install Dependencies
```bash
composer install
```

### 2. إعداد قاعدة البيانات - Setup Database
```bash
php artisan migrate
```

### 3. تشغيل الخادم - Start Server
```bash
php artisan serve
```

### 4. اختبار النظام - Test the System
```bash
php artisan test
```

## ملاحظات مهمة - Important Notes

1. **تأكد من تكوين البريد الإلكتروني** قبل استخدام ميزة OTP
2. **قم بتحديث متغيرات البيئة** حسب بيئتك
3. **استخدم HTTPS** في الإنتاج للحماية الإضافية
4. **راقب سجلات النظام** للكشف عن أي محاولات اختراق
5. **قم بعمل نسخ احتياطية** لقاعدة البيانات بانتظام

## الدعم - Support

للمساعدة أو الاستفسارات، يرجى التواصل مع فريق التطوير.

For help or inquiries, please contact the development team.
