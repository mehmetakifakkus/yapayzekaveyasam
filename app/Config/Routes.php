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

// Tags
$routes->get('tag/(:segment)', 'Tags::show/$1');
$routes->get('api/tags/search', 'Tags::search');

// Users
$routes->get('user/(:num)', 'Users::profile/$1');
$routes->get('user/(:num)/bookmarks', 'Users::bookmarks/$1');
$routes->post('user/update-bio', 'Users::updateBio');
$routes->post('user/update-theme', 'Users::updateTheme');

// Notifications
$routes->get('notifications', 'Notifications::index');
$routes->post('notifications/mark-all-read', 'Notifications::markAllRead');

// Feed
$routes->get('feed', 'Users::feed');

// API endpoints
$routes->group('api', function($routes) {
    $routes->post('like/(:num)', 'Api::like/$1');
    $routes->post('comment', 'Api::comment');
    $routes->delete('comment/(:num)', 'Api::deleteComment/$1');
    $routes->get('projects', 'Api::projects');

    // Bookmarks
    $routes->post('bookmark/(:num)', 'Api::bookmark/$1');

    // Follow
    $routes->post('follow/(:num)', 'Api::follow/$1');

    // Notifications
    $routes->get('notifications', 'Notifications::getRecent');
    $routes->post('notifications/(:num)/read', 'Notifications::markRead/$1');
    $routes->get('notifications/unread-count', 'Notifications::unreadCount');
});

// Admin
$routes->group('admin', function($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('projects', 'Admin::projects');
    $routes->post('projects/(:num)/approve', 'Admin::approveProject/$1');
    $routes->post('projects/(:num)/reject', 'Admin::rejectProject/$1');
    $routes->delete('projects/(:num)', 'Admin::deleteProject/$1');
    $routes->post('projects/(:num)/refresh-screenshot', 'Admin::refreshScreenshot/$1');
    $routes->get('users', 'Admin::users');
    $routes->post('users/(:num)/ban', 'Admin::banUser/$1');
    $routes->post('users/(:num)/unban', 'Admin::unbanUser/$1');
    $routes->get('settings', 'Admin::settings');
    $routes->post('settings', 'Admin::updateSettings');
});
