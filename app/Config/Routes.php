<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Home
$routes->get('/', 'Home::index');

// Auth
$routes->get('auth/google', 'Auth::google');
$routes->get('auth/callback', 'Auth::callback');
$routes->get('auth/logout', 'Auth::logout');

// Projects
$routes->get('projects', 'Projects::index');
$routes->get('projects/create', 'Projects::create');
$routes->post('projects/store', 'Projects::store');
$routes->get('projects/(:segment)/edit', 'Projects::edit/$1');
$routes->post('projects/(:segment)/update', 'Projects::update/$1');
$routes->delete('projects/(:segment)/delete', 'Projects::delete/$1');
$routes->get('projects/(:segment)', 'Projects::show/$1');

// Categories
$routes->get('category/(:segment)', 'Categories::show/$1');

// AI Tools
$routes->get('tool/(:segment)', 'Tools::show/$1');

// Users
$routes->get('user/(:num)', 'Users::profile/$1');
$routes->post('user/update-bio', 'Users::updateBio');

// API endpoints
$routes->group('api', function($routes) {
    $routes->post('like/(:num)', 'Api::like/$1');
    $routes->post('comment', 'Api::comment');
    $routes->delete('comment/(:num)', 'Api::deleteComment/$1');
    $routes->get('projects', 'Api::projects');
});

// Admin
$routes->group('admin', function($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('projects', 'Admin::projects');
    $routes->post('projects/(:num)/approve', 'Admin::approveProject/$1');
    $routes->post('projects/(:num)/reject', 'Admin::rejectProject/$1');
    $routes->delete('projects/(:num)', 'Admin::deleteProject/$1');
    $routes->get('users', 'Admin::users');
    $routes->post('users/(:num)/ban', 'Admin::banUser/$1');
    $routes->post('users/(:num)/unban', 'Admin::unbanUser/$1');
});
