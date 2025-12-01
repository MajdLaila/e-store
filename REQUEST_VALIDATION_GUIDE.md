# ملفات Request للتحقق من صحة البيانات - Request Validation Files

## نظرة عامة - Overview

تم إنشاء مجموعة شاملة من ملفات Request للتحقق من صحة البيانات في نظام المصادقة. هذه الملفات تساعد في تنظيم قواعد التحقق وجعل الكود أكثر تنظيماً وأماناً.

A comprehensive set of Request files has been created for data validation in the authentication system. These files help organize validation rules and make the code more structured and secure.

## الملفات المتاحة - Available Files

### 1. RegisterRequest
**الغرض**: التحقق من صحة بيانات تسجيل المستخدم الجديد
**Purpose**: Validate new user registration data

**الحقول المطلوبة - Required Fields**:
- `name`: الاسم (مطلوب، نص، 2-255 حرف)
- `email`: البريد الإلكتروني (مطلوب، إيميل صحيح، فريد)
- `password`: كلمة المرور (مطلوب، 8 أحرف على الأقل، تحتوي على حرف كبير وصغير ورقم)
- `password_confirmation`: تأكيد كلمة المرور (مطلوب، مطابق لكلمة المرور)

**الحقول الاختيارية - Optional Fields**:
- `phone`: رقم الهاتف (اختياري، نص، 20 رقم كحد أقصى)

**قواعد التحقق المتقدمة - Advanced Validation Rules**:
```php
'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
'phone' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/'
```

### 2. LoginRequest
**الغرض**: التحقق من صحة بيانات تسجيل الدخول
**Purpose**: Validate login data

**الحقول المطلوبة - Required Fields**:
- `email`: البريد الإلكتروني (مطلوب، إيميل صحيح)
- `password`: كلمة المرور (مطلوب)

### 3. OtpRequest
**الغرض**: التحقق من صحة بيانات أكواد OTP
**Purpose**: Validate OTP data

**الحقول المطلوبة - Required Fields**:
- `email`: البريد الإلكتروني (مطلوب، إيميل صحيح)
- `code`: كود التحقق (مطلوب، 6 أرقام بالضبط)
- `purpose`: الغرض من التحقق (مطلوب، أحد القيم: register, login, reset_password, verify_email)

**قواعد التحقق المتقدمة - Advanced Validation Rules**:
```php
'code' => 'required|string|size:6|regex:/^[0-9]{6}$/'
'purpose' => 'required|in:register,login,reset_password,verify_email'
```

### 4. ProfileUpdateRequest
**الغرض**: التحقق من صحة بيانات تحديث الملف الشخصي
**Purpose**: Validate profile update data

**الحقول الاختيارية - Optional Fields**:
- `name`: الاسم (اختياري، نص، 2-255 حرف)
- `phone`: رقم الهاتف (اختياري، نص، 20 رقم كحد أقصى)
- `avatar`: رابط الصورة الشخصية (اختياري، رابط صحيح)
- `lat`: خط العرض (اختياري، رقم بين -90 و 90)
- `lang`: خط الطول (اختياري، رقم بين -180 و 180)

**قواعد التحقق المتقدمة - Advanced Validation Rules**:
```php
'avatar' => 'sometimes|nullable|string|url|max:500'
'lat' => 'sometimes|nullable|numeric|between:-90,90'
'lang' => 'sometimes|nullable|numeric|between:-180,180'
```

### 5. ChangePasswordRequest
**الغرض**: التحقق من صحة بيانات تغيير كلمة المرور
**Purpose**: Validate password change data

**الحقول المطلوبة - Required Fields**:
- `current_password`: كلمة المرور الحالية (مطلوب)
- `password`: كلمة المرور الجديدة (مطلوب، 8 أحرف على الأقل، تحتوي على حرف كبير وصغير ورقم)
- `password_confirmation`: تأكيد كلمة المرور الجديدة (مطلوب، مطابق لكلمة المرور الجديدة)

### 6. SocialLoginRequest
**الغرض**: التحقق من صحة بيانات تسجيل الدخول الاجتماعي
**Purpose**: Validate social login data

**الحقول المطلوبة - Required Fields**:
- `email`: البريد الإلكتروني (مطلوب، إيميل صحيح)
- `name`: الاسم (مطلوب، نص، 2-255 حرف)
- `avatar`: رابط الصورة الشخصية (اختياري، رابط صحيح)

**الحقول الخاصة بالمزود - Provider-Specific Fields**:
- `google_id`: معرف Google (مطلوب عند استخدام Google)
- `facebook_id`: معرف Facebook (مطلوب عند استخدام Facebook)
- `provider`: مزود الخدمة (مطلوب، google أو facebook)
- `provider_id`: معرف المزود (مطلوب)

## الميزات المتقدمة - Advanced Features

### 1. رسائل الخطأ المخصصة - Custom Error Messages
جميع ملفات Request تحتوي على رسائل خطأ باللغة العربية:

```php
public function messages(): array
{
    return [
        'name.required' => 'الاسم مطلوب',
        'email.email' => 'البريد الإلكتروني غير صحيح',
        'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وحرف صغير ورقم',
    ];
}
```

### 2. أسماء الحقول المخصصة - Custom Field Names
جميع ملفات Request تحتوي على أسماء حقول باللغة العربية:

```php
public function attributes(): array
{
    return [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
    ];
}
```

### 3. التحقق الديناميكي - Dynamic Validation
بعض ملفات Request تحتوي على تحقق ديناميكي حسب السياق:

```php
// في SocialLoginRequest
$provider = $this->route('provider') ?? $this->input('provider');

if ($provider === 'google') {
    $rules['google_id'] = 'required|string|max:255';
} elseif ($provider === 'facebook') {
    $rules['facebook_id'] = 'required|string|max:255';
}
```

## كيفية الاستخدام - Usage

### 1. في المتحكمات - In Controllers
```php
use App\Http\Requests\RegisterRequest;

public function register(RegisterRequest $request): JsonResponse
{
    // البيانات محققة بالفعل
    $user = User::create($request->validated());
    
    return response()->json([
        'success' => true,
        'data' => $user
    ]);
}
```

### 2. التحقق التلقائي - Automatic Validation
Laravel يقوم بالتحقق من البيانات تلقائياً قبل تنفيذ الدالة:

```php
// إذا فشل التحقق، يتم إرجاع رسالة خطأ تلقائياً
// If validation fails, error message is returned automatically
```

### 3. الوصول للبيانات المحققة - Access Validated Data
```php
// الحصول على البيانات المحققة فقط
$validatedData = $request->validated();

// الحصول على حقل محدد
$email = $request->email;
$name = $request->name;
```

## أمثلة على الاستخدام - Usage Examples

### 1. تسجيل مستخدم جديد
```bash
POST /api/auth/register
{
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "password": "Password123",
    "password_confirmation": "Password123",
    "phone": "+966501234567"
}
```

### 2. تسجيل الدخول
```bash
POST /api/auth/login
{
    "email": "ahmed@example.com",
    "password": "Password123"
}
```

### 3. التحقق من OTP
```bash
POST /api/auth/verify-otp
{
    "email": "ahmed@example.com",
    "code": "123456",
    "purpose": "verify_email"
}
```

### 4. تحديث الملف الشخصي
```bash
PUT /api/auth/profile
{
    "name": "أحمد محمد الجديد",
    "phone": "+966501234568",
    "lat": 24.7136,
    "lang": 46.6753
}
```

### 5. تغيير كلمة المرور
```bash
POST /api/auth/change-password
{
    "current_password": "OldPassword123",
    "password": "NewPassword123",
    "password_confirmation": "NewPassword123"
}
```

### 6. تسجيل الدخول الاجتماعي
```bash
POST /api/auth/google
{
    "google_id": "google_user_id_123",
    "email": "user@gmail.com",
    "name": "User Name",
    "avatar": "https://example.com/avatar.jpg"
}
```

## الأمان والحماية - Security & Protection

### 1. التحقق من قوة كلمة المرور
```php
'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
```
- يجب أن تحتوي على حرف صغير
- يجب أن تحتوي على حرف كبير
- يجب أن تحتوي على رقم واحد على الأقل

### 2. التحقق من صحة البريد الإلكتروني
```php
'email|max:255|unique:users,email'
```
- تنسيق صحيح للبريد الإلكتروني
- طول مناسب
- فريد في قاعدة البيانات

### 3. التحقق من صحة رقم الهاتف
```php
'regex:/^[\+]?[0-9\s\-\(\)]+$/'
```
- دعم الرموز الدولية
- دعم المسافات والأقواس
- أرقام فقط

### 4. التحقق من صحة الإحداثيات الجغرافية
```php
'lat' => 'sometimes|nullable|numeric|between:-90,90'
'lang' => 'sometimes|nullable|numeric|between:-180,180'
```
- خط العرض: بين -90 و 90
- خط الطول: بين -180 و 180

## المزايا - Benefits

### 1. تنظيم الكود - Code Organization
- فصل قواعد التحقق عن منطق العمل
- إعادة استخدام قواعد التحقق
- سهولة الصيانة والتطوير

### 2. الأمان - Security
- التحقق من جميع المدخلات
- منع حقن البيانات الضارة
- حماية من هجمات CSRF

### 3. تجربة المستخدم - User Experience
- رسائل خطأ واضحة باللغة العربية
- تحقق فوري من البيانات
- توجيه المستخدم لإصلاح الأخطاء

### 4. الأداء - Performance
- التحقق على مستوى الخادم
- تقليل العمليات غير الضرورية
- تحسين استجابة التطبيق

## التطوير المستقبلي - Future Development

### 1. إضافة قواعد تحقق جديدة
- التحقق من صحة أرقام الهوية
- التحقق من صحة أرقام الحساب البنكي
- التحقق من صحة أرقام الرخصة

### 2. دعم لغات متعددة
- إضافة رسائل خطأ بالإنجليزية
- دعم التبديل بين اللغات
- تخصيص الرسائل حسب المنطقة

### 3. التحقق المتقدم
- التحقق من صحة الصور المرفوعة
- التحقق من صحة الملفات
- التحقق من صحة الروابط الخارجية

## الخلاصة - Conclusion

ملفات Request توفر نظام تحقق شامل ومتقدم لجميع عمليات المصادقة في التطبيق. هذه الملفات تساعد في:

- **تنظيم الكود**: فصل قواعد التحقق عن منطق العمل
- **تحسين الأمان**: التحقق من جميع المدخلات قبل المعالجة
- **تحسين تجربة المستخدم**: رسائل خطأ واضحة ومفيدة
- **سهولة الصيانة**: إمكانية تعديل قواعد التحقق بسهولة

Request files provide a comprehensive and advanced validation system for all authentication operations in the application. These files help in:

- **Code Organization**: Separating validation rules from business logic
- **Security Enhancement**: Validating all inputs before processing
- **User Experience Improvement**: Clear and helpful error messages
- **Easy Maintenance**: Ability to modify validation rules easily
