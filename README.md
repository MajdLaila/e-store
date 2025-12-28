 #  E-Store

A scalable RESTful API built with Laravel using Clean Architecture
(Controller → Service → Repository) for e-commerce platforms.

---

## 🚀 Overview

This project is a scalable and maintainable application built using best practices and clean architecture principles. It is designed to be easy to understand, extend, and test.

---
## User flow

* User registers and logs in.

* User sets their location for product delivery.

* User adds products to cart.

* User selects a shipping address (either their location or another).

* The system automatically suggests the nearest shipping company.

* User can chat directly with the product owner.

* Favorites and cart management are supported for seamless shopping.


## 🧰 Tech Stack

* Laravel 10
* PHP 8.2
* MySQL
* Laravel Sanctum (Authentication)
* REST API
* Clean Architecture

---

## ✨ Key Features

- User authentication & authorization
- Category hierarchy (parent / child)
- Products & Ads management
- OTP verification & password reset
- Secure API with Sanctum
- Clean & testable architecture

---

## Project Architecture

This project follows a clean layered architecture:

Controller → Service → Repository → Model

### Layer Responsibilities

- **Controller**
  - Handles HTTP requests & responses
  - Request validation
  - Delegates logic to services

- **Service**
  - Contains business logic
  - Handles transactions & complex operations

- **Repository**
  - Handles database queries
  - Abstracts data access using Eloquent

---

## 📂 Folder Structure


app/
├── Exceptions
│   └── Handler.php
├── Http
│   └── Controllers
├── Services
├── Repositories
├── Models
├── Requests
└── Routes


## 🔌 API Documentation

## Postman Doc URL : https://documenter.getpostman.com/view/31206715/2sB3WwoweF


### Base URL

```
/api/v1
```

### Example Endpoints

##  Authentication

- Prefix : /api/v1/auth

Method	Endpoint	Description	Auth
POST	/auth/user/login	User login
POST	/auth/user/register	User registration
POST	/auth/send-otp	Send email OTP
POST	/auth/verify-otp	Verify OTP
POST	/auth/forgot-password	Request password reset
POST	/auth/reset-password	Reset password Users

##   Prefix: /api/v1/user (Sanctum protected)

Method	Endpoint	Description
GET	/user/all	Get all users
GET	/user	Get authenticated user info
GET	/user/{id}	Get user by ID
PUT	/user/{id}	Update user
DELETE	/user/{id}	Delete user

##  Categories

Prefix: /api/v1/categories (Sanctum protected)

Method	Endpoint	Description
GET	/categories	Get all categories
POST	/categories	Create category
GET	/categories/{id}	Get category
PUT	/categories/{id}	Update category
DELETE	/categories/{id}	Delete category
GET	/categories/{id}/children	Get child categories
GET	/categories/{id}/products	Get category products

##   Products

Prefix: /api/v1/products (Sanctum protected)

Method	Endpoint	Description
GET	/products	List products
POST	/products	Create product
GET	/products/{id}	Get product
PATCH	/products/{id}	Update product
DELETE	/products/{id}	Delete product

##   Ads

Prefix: /api/v1/ads (Sanctum protected)

Method	Endpoint	Description
GET	/ads	List ads
POST	/ads	Create ad
GET	/ads/{id}	Get ad
PUT	/ads/{id}	Update ad
DELETE	/ads/{id}	Delete ad

---

## ⚙️ Installation & Setup

```bash
git clone https://github.com/MajdLaila/e-store.git
cd e-store
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

```

---

## 🔐 Environment Variables

Make sure to configure the following variables in your `.env` file:

* DB_DATABASE
* DB_USERNAME
* DB_PASSWORD
* APP_URL

---

## 🧪 Testing

🚧 Under development

---

## 📌 Notes

* This project follows best practices for scalability and maintainability.


---

## 📬 Contact

**Author:** Majd Laila
**Email:** [majdlila777@gmail.com]
**LinkedIn** [www.linkedin.com/in/majd-laila]
