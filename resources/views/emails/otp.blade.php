@component('mail::message')
# {{ $purpose === 'register' ? 'Complete Your Registration' : 'Email Verification' }}

@if($purpose === 'register')
Welcome! To complete your registration, please use the verification code below:
@elseif($purpose === 'login')
You requested a login verification code. Use the code below to sign in:
@elseif($purpose === 'reset_password')
You requested to reset your password. Use the verification code below:
@elseif($purpose === 'verify_email')
Please verify your email address using the code below:
@else
Please use the verification code below:
@endif

@component('mail::panel')
**Your verification code is: {{ $otpCode }}**
@endcomponent

**Important Security Information:**
- This code will expire in 10 minutes
- Never share this code with anyone
- If you didn't request this code, please ignore this email

@if($purpose === 'register')
If you have any questions, please contact our support team.
@else
If you didn't request this verification code, please contact our support team immediately.
@endif

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
