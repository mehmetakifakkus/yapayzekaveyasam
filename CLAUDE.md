# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

AI Showcase is a CodeIgniter 4 web application for showcasing projects built with AI tools (Claude Code, Cursor, Windsurf, etc.). Users can browse projects, authenticate via Google OAuth, submit their own projects, and interact through likes and comments.

## Essential Commands

```bash
# Development server (PHP 8.1+ required)
php spark serve

# Database
php spark migrate                    # Run migrations
php spark db:seed DatabaseSeeder     # Seed categories and AI tools

# Frontend (Tailwind CSS)
npm run build                        # Compile CSS (minified)
npm run watch                        # Watch for changes

# Testing
composer test                        # Run PHPUnit tests
./vendor/bin/phpunit                 # Alternative test command
```

## Architecture

### MVC Pattern

**Controllers** (`app/Controllers/`):
- Extend `BaseController` which provides `isLoggedIn()`, `getCurrentUser()`, `requireAuth()`, `isAdmin()`, `requireAdmin()`, and `getViewData()`
- `Api.php` handles AJAX endpoints returning JSON responses
- `Admin.php` handles admin panel (dashboard, project/user management)
- Always use `$this->getViewData($data)` when passing data to views

**Models** (`app/Models/`):
- Extend CodeIgniter's Model class with custom query methods
- Validation rules defined in `$validationRules` property
- Key models: `ProjectModel` (with filtering/sorting), `LikeModel` (toggle logic), `CommentModel`

**Views** (`app/Views/`):
- `layouts/main.php` - Master layout
- `components/` - Reusable UI (navbar, footer, project_card)
- `pages/` - Page templates
- Use Tailwind CSS utility classes

### Database Schema

7 tables with foreign key relationships:
- `users` (Google OAuth)
- `categories`, `ai_tools` (lookup tables with slugs)
- `projects` (status: pending/approved/rejected)
- `project_ai_tools` (many-to-many junction)
- `likes`, `comments` (user interactions)

### Routes (`app/Config/Routes.php`)

```
/projects/(:segment)       → Projects::show/$1    (slug-based)
/category/(:segment)       → Categories::show/$1
/tool/(:segment)           → Tools::show/$1
/user/(:num)               → Users::profile/$1
/api/like/(:num)           → Api::like/$1         (POST, returns JSON)
/api/comment               → Api::comment         (POST, returns JSON)

# Admin routes (requires admin)
/admin                     → Admin::index         (dashboard)
/admin/projects            → Admin::projects      (project management)
/admin/users               → Admin::users         (user management)
```

### Authentication Flow

Google OAuth implemented in `Auth.php`:
1. `/auth/google` → Redirect to Google
2. `/auth/callback` → Exchange code, create/update user, set session
3. Session stores `user_id`; `BaseController` loads user on each request

### Admin System

Admin users are determined by email in `.env`:
```
admin.emails = 'admin@example.com,another@example.com'
```

- `BaseController::isAdmin()` checks if current user's email is in the list
- `BaseController::requireAdmin()` enforces admin access (throws 404 if not admin)
- New projects default to `pending` status (auto-approved if submitted by admin)
- Admin can approve/reject/delete projects and ban/unban users

### Frontend

- **Tailwind CSS v4** - Source: `src/input.css`, Output: `public/assets/css/app.css`
- Custom utility classes: `glass-card`, `btn-primary`, `btn-secondary`, `input-field`, `project-card`, `ai-badge`
- JavaScript: `public/assets/js/app.js` (toast notifications, helpers)

## Configuration

Environment variables in `.env`:
```
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'
database.default.* = MySQL settings
google.clientId = OAuth client ID
google.clientSecret = OAuth secret
admin.emails = Comma-separated admin email addresses
```

## Key Patterns

**Project filtering** (`ProjectModel::getApprovedProjects`):
```php
$filters = ['category' => 'slug', 'ai_tool' => 'slug', 'sort' => 'newest|trending|popular', 'search' => 'query'];
```

**Enriching projects with related data**:
```php
$project['likes_count'] = $this->likeModel->getCountByProject($id);
$project['ai_tools'] = $this->aiToolModel->getByProjectId($id);
$project['is_liked'] = $this->likeModel->hasLiked($userId, $id);
```

**API response format**:
```php
return $this->respond(['success' => true, 'data' => $data, 'message' => '...']);
```
