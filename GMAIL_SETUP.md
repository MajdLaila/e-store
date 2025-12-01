# إعداد Gmail لإرسال البريد الإلكتروني

## المشكلة
Gmail لا يقبل كلمة المرور العادية للمصادقة. يجب استخدام "App Password" (كلمة مرور التطبيق).

## خطوات الحل

### 1. تفعيل التحقق بخطوتين (Two-Factor Authentication)
1. اذهب إلى [حساب Google](https://myaccount.google.com/)
2. اختر **الأمان** (Security)
3. في قسم "تسجيل الدخول إلى Google"، اضغط على **التحقق بخطوتين** (2-Step Verification)
4. اتبع الخطوات لتفعيل التحقق بخطوتين

### 2. إنشاء App Password (كلمة مرور التطبيق)
1. بعد تفعيل التحقق بخطوتين، اذهب إلى [App Passwords](https://myaccount.google.com/apppasswords)
   - أو من صفحة الأمان، ابحث عن **كلمات مرور التطبيقات** (App passwords)
2. اختر **البريد** (Mail) كنوع التطبيق
3. اختر **الكمبيوتر** (Computer) أو **Windows Computer** كجهاز
4. اضغط **إنشاء** (Generate)
5. **انسخ كلمة المرور التي تظهر** (16 حرف بدون مسافات)

### 3. تحديث ملف `.env`
أضف أو حدث الإعدادات التالية في ملف `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=majdm866@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=majdm866@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**ملاحظات مهمة:**
- استخدم **App Password** (كلمة مرور التطبيق) في `MAIL_PASSWORD` وليس كلمة المرور العادية
- لا تضع مسافات في App Password (إذا كانت تحتوي على مسافات، احذفها)
- `MAIL_USERNAME` يجب أن يكون نفس البريد الإلكتروني المستخدم في Gmail

### 4. مسح الكاش
بعد تحديث ملف `.env`، قم بمسح الكاش:

```bash
php artisan config:clear
php artisan cache:clear
```

### 5. اختبار الإرسال
جرب إرسال OTP مرة أخرى من خلال API:
```
POST /api/auth/send-otp
{
    "email": "user@example.com",
    "purpose": "verify_email"
}
```

## بدائل أخرى

### استخدام Mailtrap (للتطوير)
إذا كنت تريد اختبار البريد الإلكتروني بدون إعداد Gmail:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

### استخدام Mailgun أو SendGrid
يمكنك استخدام خدمات بريد إلكتروني احترافية مثل Mailgun أو SendGrid.

## استكشاف الأخطاء

### الخطأ: "Username and Password not accepted"
- تأكد من استخدام **App Password** وليس كلمة المرور العادية
- تأكد من تفعيل التحقق بخطوتين
- تأكد من عدم وجود مسافات في App Password

### الخطأ: "Connection timeout"
- تأكد من أن `MAIL_PORT=587` و `MAIL_ENCRYPTION=tls`
- تأكد من أن جدار الحماية لا يحجب المنفذ 587

### البريد لا يصل
- تحقق من مجلد "الرسائل غير المرغوب فيها" (Spam)
- تأكد من أن `MAIL_FROM_ADDRESS` مطابق لـ `MAIL_USERNAME`

