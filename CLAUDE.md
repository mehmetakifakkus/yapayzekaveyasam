# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

AI Showcase is a CodeIgniter 4 web application for showcasing projects built with AI tools (Claude Code, Cursor, Windsurf, etc.). Users can browse projects, authenticate via Google OAuth, submit their own projects, and interact through likes, comments, bookmarks, and follows.

## Essential Commands

```bash
# Development server (PHP 8.1+ required)
php spark serve

# Database
php spark migrate                    # Run migrations
php spark db:seed DatabaseSeeder     # Seed categories, AI tools, and badges

# Frontend (Tailwind CSS v4)
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
- `Admin.php` handles admin panel (dashboard, project/user management, analytics)
- Always use `$this->getViewData($data)` when passing data to views

**Models** (`app/Models/`):
- Extend CodeIgniter's Model class with custom query methods
- Validation rules defined in `$validationRules` property
- Key models: `ProjectModel` (filtering/sorting), `LikeModel`, `CommentModel`, `NotificationModel`, `BadgeModel`, `FollowModel`, `BookmarkModel`

**Views** (`app/Views/`):
- `layouts/main.php` - Master layout with theme support
- `components/` - Reusable UI (navbar, footer, project_card)
- `pages/` - Page templates
- Use Tailwind CSS utility classes

### Database Schema

16 tables with foreign key relationships:
- `users` (Google OAuth, theme preference, email_digest setting)
- `categories`, `ai_tools` (lookup tables with slugs)
- `projects` (status: pending/approved/rejected)
- `project_ai_tools`, `project_tags` (many-to-many junctions)
- `tags` (user-defined project tags)
- `likes`, `comments`, `comment_likes` (user interactions)
- `bookmarks`, `follows` (user relationships)
- `notifications` (types: like, comment, follow, project_approved, project_rejected, badge, comment_like, mention)
- `badges`, `user_badges` (gamification system)
- `weekly_highlights` (featured projects)

### Key Routes

```
# Public
/projects/(:segment)           → Projects::show/$1 (slug-based)
/category/(:segment)           → Categories::show/$1
/tool/(:segment)               → Tools::show/$1
/tag/(:segment)                → Tags::show/$1
/user/(:num)                   → Users::profile/$1
/feed                          → Users::feed (followed users' projects)

# API (POST, returns JSON)
/api/like/(:num)               → Toggle project like
/api/like/comment/(:num)       → Toggle comment like
/api/bookmark/(:num)           → Toggle bookmark
/api/follow/(:num)             → Toggle follow
/api/comment                   → Add comment (supports @mentions)

# Admin (requires admin)
/admin                         → Dashboard with stats
/admin/projects                → Project management
/admin/users                   → User management
/admin/analytics               → Analytics dashboard
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

### Notification System

`NotificationModel` creates notifications for:
- Project likes, comments, approvals, rejections
- New followers
- Comment likes and @mentions
- Badge achievements

### Badge System

`BadgeService` (`app/Services/BadgeService.php`) checks and awards badges based on:
- Project count thresholds (1, 5, 10 projects)
- Total likes received (10, 50, 100 likes)
- Follower count (10 followers)

### Theme System

Users can select themes stored in `users.theme` column. Available themes defined in `APP_THEME` env variable or user preference. Theme CSS variables in `src/input.css`.

## Configuration

Environment variables in `.env`:
```
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'
database.default.* = MySQL settings
google.clientId = OAuth client ID
google.clientSecret = OAuth secret
google.redirectUri = OAuth callback URL
admin.emails = Comma-separated admin email addresses
APP_THEME = default theme (default, emerald, amber, ocean, mono, light-*)
```

### Cloudflare Proxy Support

`App.php` includes Cloudflare IP ranges in `$proxyIPs` for proper client IP detection behind Cloudflare proxy.

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
$project['is_bookmarked'] = $this->bookmarkModel->hasBookmarked($userId, $id);
$project['tags'] = $this->tagModel->getByProjectId($id);
```

**API response format**:
```php
return $this->respond(['success' => true, 'data' => $data, 'message' => '...']);
```

**@Mention parsing** in comments:
```php
// Api.php extracts @username mentions and creates notifications
preg_match_all('/@(\w+)/', $content, $matches);
```
