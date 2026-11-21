<?php

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Recipe & Meal Prep Tracker API',
    description: 'RESTful backend API built with FlightPHP, PDO, JWT authentication, Role-Based Access Control, and TheMealDB external integration.'
)]
#[OA\Server(
    url: '/AhmadAlomarWP/IT-2001-Web-Programming--Ahmad-Alabd-Alomar/backend',
    description: 'Local XAMPP Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Enter the JWT token returned from /auth/login or /auth/register'
)]
class OpenApiSpec
{
}