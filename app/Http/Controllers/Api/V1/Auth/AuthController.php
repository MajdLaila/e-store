<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\ProfileUpdateRequest;
use App\Http\Requests\Api\V1\Auth\SendOtpRequest;
use App\Http\Requests\Api\V1\Auth\OtpRequest;
use App\Http\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseApiController
{
  public function __construct(
    private readonly AuthService $Authservice,
  ) {}

  // تسجيل مستخدم جديد
  public function register(RegisterRequest $request): JsonResponse
  {
    $data = $this->Authservice->registeruser($request->validated());

    return $this->successResponse(
      message: 'register done',
      statusCode: Response::HTTP_OK,
      data: $data,
    );
  }

  // تسجيل الدخول
  public function login(LoginRequest $request): JsonResponse
  {
    $credentials = $request->only(['email', 'password']);
    $data = $this->Authservice->authuser($credentials);

    return $this->successResponse(
      message: 'login done',
      statusCode: Response::HTTP_OK,
      data: $data,
    );
  }

  // إرسال OTP
  public function sendOtp(SendOtpRequest $request): JsonResponse
  {
    $data = $this->Authservice->sendOtpCode($request->validated());

    return $this->successResponse(
      message: 'OTP sent successfully',
      statusCode: Response::HTTP_OK,
      data: $data,
    );
  }

  // التحقق من OTP
  public function verifyOtp(OtpRequest $request): JsonResponse
  {
    $data = $this->Authservice->verifyOtpCode($request->validated());

    return $this->successResponse(
      message: 'OTP verified successfully',
      statusCode: Response::HTTP_OK,
      data: $data,
    );
  }

  // تسجيل الخروج
  public function logout(Request $request): JsonResponse
  {
    $request->user()->currentAccessToken()->delete();

    return $this->successResponse(
      message: 'Logged out successfully',
      statusCode: Response::HTTP_OK
    );
  }

  // عرض الملف الشخصي
  public function profile(Request $request): JsonResponse
  {
    $user = $request->user();

    return $this->successResponse(
      message: 'Profile retrieved successfully',
      statusCode: Response::HTTP_OK,
      data: [
        'user' => $user,
        'is_email_verified' => $user->isEmailVerified(),
        'is_phone_verified' => $user->isPhoneVerified(),
        'is_admin' => $user->isAdmin(),
        'is_active' => $user->isActive(),
      ]
    );
  }

  // تحديث الملف الشخصي
  public function updateProfile(ProfileUpdateRequest $request): JsonResponse
  {
    $user = $request->user();
    $user->update($request->only(['name', 'phone', 'avatar', 'lat', 'lang']));

    return $this->successResponse(
      message: 'Profile updated successfully',
      statusCode: Response::HTTP_OK,
      data: ['user' => $user]
    );
  }
}
