<?php

namespace Tests\Feature;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OtpRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\SocialLoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RequestValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_request_validation()
    {
        // Test valid data
        $validData = [
            'name' => 'أحمد محمد',
            'email' => 'ahmed@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'phone' => '+966501234567'
        ];

        $request = new RegisterRequest();
        $validator = Validator::make($validData, $request->rules(), $request->messages());
        
        $this->assertTrue($validator->passes());

        // Test invalid data
        $invalidData = [
            'name' => 'A', // Too short
            'email' => 'invalid-email', // Invalid email
            'password' => '123', // Too short and weak
            'password_confirmation' => '456', // Doesn't match
        ];

        $validator = Validator::make($invalidData, $request->rules(), $request->messages());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_login_request_validation()
    {
        $request = new LoginRequest();
        
        // Test valid data
        $validData = [
            'email' => 'ahmed@example.com',
            'password' => 'password123'
        ];

        $validator = Validator::make($validData, $request->rules(), $request->messages());
        $this->assertTrue($validator->passes());

        // Test invalid data
        $invalidData = [
            'email' => 'invalid-email',
            'password' => ''
        ];

        $validator = Validator::make($invalidData, $request->rules(), $request->messages());
        $this->assertFalse($validator->passes());
    }

    public function test_otp_request_validation()
    {
        $request = new OtpRequest();
        
        // Test valid data
        $validData = [
            'email' => 'ahmed@example.com',
            'code' => '123456',
            'purpose' => 'verify_email'
        ];

        $validator = Validator::make($validData, $request->rules(), $request->messages());
        $this->assertTrue($validator->passes());

        // Test invalid data
        $invalidData = [
            'email' => 'invalid-email',
            'code' => '123', // Too short
            'purpose' => 'invalid_purpose'
        ];

        $validator = Validator::make($invalidData, $request->rules(), $request->messages());
        $this->assertFalse($validator->passes());
    }

    public function test_profile_update_request_validation()
    {
        $request = new ProfileUpdateRequest();
        
        // Test valid data
        $validData = [
            'name' => 'أحمد محمد الجديد',
            'phone' => '+966501234568',
            'avatar' => 'https://example.com/avatar.jpg',
            'lat' => 24.7136,
            'lang' => 46.6753
        ];

        $validator = Validator::make($validData, $request->rules(), $request->messages());
        $this->assertTrue($validator->passes());

        // Test invalid data
        $invalidData = [
            'name' => 'A', // Too short
            'phone' => 'invalid-phone',
            'avatar' => 'not-a-url',
            'lat' => 200, // Out of range
            'lang' => -200 // Out of range
        ];

        $validator = Validator::make($invalidData, $request->rules(), $request->messages());
        $this->assertFalse($validator->passes());
    }

    public function test_change_password_request_validation()
    {
        $request = new ChangePasswordRequest();
        
        // Test valid data
        $validData = [
            'current_password' => 'OldPassword123',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123'
        ];

        $validator = Validator::make($validData, $request->rules(), $request->messages());
        $this->assertTrue($validator->passes());

        // Test invalid data
        $invalidData = [
            'current_password' => '',
            'password' => '123', // Too short and weak
            'password_confirmation' => '456' // Doesn't match
        ];

        $validator = Validator::make($invalidData, $request->rules(), $request->messages());
        $this->assertFalse($validator->passes());
    }

    public function test_social_login_request_validation()
    {
        // Test Google login validation
        $googleRequest = new SocialLoginRequest();
        $googleRequest->merge(['provider' => 'google']);
        
        $validGoogleData = [
            'google_id' => 'google_user_id_123',
            'email' => 'user@gmail.com',
            'name' => 'User Name',
            'avatar' => 'https://example.com/avatar.jpg'
        ];

        $validator = Validator::make($validGoogleData, $googleRequest->rules(), $googleRequest->messages());
        $this->assertTrue($validator->passes());

        // Test Facebook login validation
        $facebookRequest = new SocialLoginRequest();
        $facebookRequest->merge(['provider' => 'facebook']);
        
        $validFacebookData = [
            'facebook_id' => 'facebook_user_id_123',
            'email' => 'user@facebook.com',
            'name' => 'User Name'
        ];

        $validator = Validator::make($validFacebookData, $facebookRequest->rules(), $facebookRequest->messages());
        $this->assertTrue($validator->passes());

        // Test general social login validation
        $generalRequest = new SocialLoginRequest();
        
        $validGeneralData = [
            'provider' => 'google',
            'provider_id' => 'provider_user_id_123',
            'email' => 'user@example.com',
            'name' => 'User Name'
        ];

        $validator = Validator::make($validGeneralData, $generalRequest->rules(), $generalRequest->messages());
        $this->assertTrue($validator->passes());

        // Test invalid data
        $invalidData = [
            'email' => 'invalid-email',
            'name' => 'A', // Too short
            'avatar' => 'not-a-url'
        ];

        $validator = Validator::make($invalidData, $generalRequest->rules(), $generalRequest->messages());
        $this->assertFalse($validator->passes());
    }

    public function test_custom_error_messages()
    {
        $request = new RegisterRequest();
        
        $invalidData = [
            'name' => '',
            'email' => 'invalid',
            'password' => '123'
        ];

        $validator = Validator::make($invalidData, $request->rules(), $request->messages());
        
        $this->assertFalse($validator->passes());
        
        // Check that custom Arabic messages are used
        $errors = $validator->errors();
        $this->assertStringContainsString('الاسم مطلوب', $errors->first('name'));
        $this->assertStringContainsString('البريد الإلكتروني غير صحيح', $errors->first('email'));
    }

    public function test_custom_attributes()
    {
        $request = new RegisterRequest();
        
        $invalidData = [
            'name' => '',
            'email' => 'invalid'
        ];

        $validator = Validator::make($invalidData, $request->rules(), $request->messages(), $request->attributes());
        
        $this->assertFalse($validator->passes());
        
        // Check that custom Arabic attributes are used
        $errors = $validator->errors();
        $this->assertStringContainsString('الاسم', $errors->first('name'));
        $this->assertStringContainsString('البريد الإلكتروني', $errors->first('email'));
    }
}
