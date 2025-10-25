# DQ-Smartplus - AI Assistant Instructions

## Project Overview
DQ-Smartplus is a PHP-based Digital Quotient assessment platform that measures students' digital intelligence across 8 categories: Screen Time, Privacy Management, Cyberbullying, Digital Citizen Identity, Digital Footprint, Cyber Security Management, Critical Thinking, and Digital Empathy.

## Architecture & Core Components

### Database Schema (MySQL)
- **account**: User management (students/admins) with USERNAME, EMAIL, PASSWORD, SCHOOL, RANK, STATE fields
- **QUESTION**: 47 questions categorized by TIPE (assessment type), supports bilingual (Indonesian/English)
- **ANSWER**: Multiple choice answers linked to questions, also bilingual
- **RESULT**: Stores user responses and calculated scores
- **VOUCHERS**: Optional access control system for premium accounts
- **VERIVICATION**: Email verification system for new registrations

### File Structure Patterns
```
/ (root)               # Main PHP files (login, quiz, reports)
/program/             # Core backend logic and includes
├── koneksi.php       # Database connection (shared across all files)
├── header.php        # Admin navigation header
├── studheader.php    # Student navigation header  
├── class.phpmailer.php # Email functionality
/assets/              # Frontend resources (Bootstrap, CSS, JS)
/chart/               # Generated score visualization charts (USER_ID + 's.jpg')
/filepdf/             # Generated PDF reports
/tcpdf/               # PDF generation library
/dompdf/              # Alternative PDF library
```

## Key Programming Patterns

### Database Connection Pattern
All PHP files use: `include 'program/koneksi.php';` to establish MySQL connection via `$conn` variable.

### Session-Based User Flow
1. **Login** (`index.php` → `prog_login.php`) validates credentials and sets `$_SESSION['ID']`
2. **State Management**: Users have STATE field controlling access:
   - 'ujian' → quiz taking (`start.php` → `quiz.php`)
   - 'upload' → image upload phase
   - 'FINISH' → results available
3. **Role-Based Access**: RANK field ('student' vs 'super' admin) determines navigation

### Quiz System Architecture
- **Sequential Navigation**: `quiz.php?no=X&answer=ENCODED_STRING`
- **Answer Encoding**: Responses stored as concatenated string format
- **Bilingual Support**: Questions/answers have separate fields for Indonesian (`QUESTION`) and English (`QUESTION_EN`)
- **Scoring Logic**: Each question has TIPE category and weighted VALUE

### Report Generation Workflow
1. **Score Calculation**: Complex SQL aggregating by category (`mail3.php`)
2. **Chart Generation**: Creates visualization saved to `/chart/USER_ID+s.jpg`
3. **PDF Generation**: Uses TCPDF to create detailed reports
4. **Email Delivery**: PHPMailer sends PDF attachments automatically

## Development Guidelines

### Database Operations
- Use prepared statements for user input (current code has SQL injection vulnerabilities)
- All queries go through `$conn` from `koneksi.php`
- Check `$result->num_rows > 0` before processing results

### Language Support
```php
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : "id";
// Use ternary operators for bilingual text
echo $lang=="id" ? "Indonesian text" : "English text";
```

### File Inclusion Pattern
```php
include 'program/koneksi.php';     // Database (required first)
include 'program/header.php';      // Admin header
include 'program/studheader.php';  // Student header  
include 'program/footer.php';      // Footer
include 'program/close.php';       // Close DB connection
```

### Error Handling & Redirects
```php
if ($conn->query($sql) === TRUE) {
    header("location: success_page.php?sukses=Success message");
} else {
    header("location: error_page.php?error=Error message");
}
exit(); // Always call after header redirects
```

## Critical Security Issues to Address
- SQL injection vulnerabilities throughout (use prepared statements)
- Plain text password storage (implement password hashing)
- Exposed email credentials in source code
- No CSRF protection on forms
- Direct file access without authentication checks

## Performance Considerations for High Concurrent Usage

### Database Bottlenecks
- **No Connection Pooling**: Each request creates new MySQL connection via `koneksi.php`
- **Heavy Aggregation Queries**: Report generation (`mail3.php`) performs complex JOINs and calculations
- **Real-time Chart Generation**: Charts created synchronously during PDF generation, blocking user response

### File System Issues
- **Chart Storage**: Static images saved to `/chart/` directory can cause I/O bottlenecks
- **PDF Generation**: TCPDF library blocks execution during report creation
- **Concurrent File Writes**: Multiple users generating reports simultaneously may cause file conflicts

### Memory & Resource Management
- **Session Storage**: PHP sessions stored on filesystem, not optimized for scale
- **Email Sending**: Synchronous SMTP operations in `mail3.php` block user flow
- **Large Result Sets**: Quiz answer processing loads all questions/answers into memory

### Scaling Recommendations
```php
// Consider implementing:
// 1. Database connection pooling
// 2. Background job queues for PDF/email generation
// 3. CDN for chart images
// 4. Redis for session storage
// 5. Async email sending
```

## Local Development Setup
- **Environment**: Laragon (Windows LAMP stack)
- **Database**: MySQL with default root/root credentials
- **Mail**: Gmail SMTP configured for notifications
- **Assets**: Bootstrap 4/5 + custom CSS themes
- **Charts**: Generated dynamically, stored as static images

When modifying this codebase, maintain the existing file inclusion patterns, respect the user state management system, and ensure bilingual support is preserved across all user-facing content.
