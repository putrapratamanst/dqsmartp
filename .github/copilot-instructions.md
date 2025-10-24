# DQ-Smartplus Digital Quotient Assessment Platform

## System Overview
A PHP-based Digital Quotient (DQ) assessment platform measuring digital intelligence across 8 categories. Built for schools and students, it provides multilingual quizzes and comprehensive reporting with chart visualizations.

## Core Architecture

### Database Structure & State Management
- **Core Tables**: 
  - `account`: Student/admin profiles and assessment state tracking
  - `QUESTION`/`ANSWER`/`RESULT`: Quiz content and responses
  - `CATEGORY`: DQ competency categories
- **Key State Fields**:
  - `account.STATE`: Controls flow (`ujian` → `upload` → `FINISH`)
  - `account.RANK`: Access control (`student`/`admin`)
  - `QUESTION.TIPE`: Maps to DQ categories 
  - `QUESTION.NILAI`: Question type (1=descriptive, else multiple choice)

### Connection & Session Patterns
- Database: Include `program/koneksi.php` for MySQL connectivity
- Session Management:
  ```php
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  ```
- Critical Session Variables:
  - `$_SESSION['ID']`: User identifier 
  - `$_SESSION['lang']`: UI language (default "id")
  - `$_SESSION['answer']`: Current quiz progress

### User Flows & Authorization
1. **Student Assessment**: 
   ```
   Login → start.php → quizstarter.php (init) → quiz.php (questions) 
   → upload.php (submit) → result.php (scores)
   ```
2. **Admin Management**:
   - Direct to `admin.php` after auth
   - Manage accounts via `activate.php`
   - Control access with `voucher.php` 

## Critical Implementation Patterns

### Answer Tracking
- Format: `L0X0X0L0` (L=selected, X=unselected, 0=delimiter)
- Usage: `$answers = explode('L', $_SESSION['answer'])`
- Initialization: See `quizstarter.php`

### Multilingual Support
- Toggle: `$_SESSION['lang']` ("id"/"en")
- Content: `QUESTION`/`QUESTION_EN` columns
- Translations: `lang.php` arrays

### Reporting Engine
- Individual: `mail3.php` (radar charts)
- Aggregate: `report_pdf.php` (school comparisons) 
- Data Queries:
  ```sql
  SELECT SUM(CASE WHEN Q.TIPE = ? THEN R.VALUE ELSE 0 END) 
  FROM RESULT R JOIN QUESTION Q 
  ```

## Development Workflow

### Environment Setup
1. XAMPP/Laragon with MySQL
2. Import `dqsmartp_db` schema
3. Configure `program/koneksi.php`

### Key Dependencies
- **PDF**: DompDF (primary), TCPDF (auxiliary)
- **Charts**: Chart.js for visualizations
- **Email**: PHPMailer for results delivery
- **UI**: Bootstrap + jQuery

### File Structure
- `/program`: Core includes and utilities
- `/assets`: Frontend frameworks and styles
- `/report`: CodeIgniter reporting module
- Root: Main application endpoints

### Version Control Notes
- Backup files use date suffixes (e.g., `_20220918`)
- Avoid editing versioned files directly
- Focus changes on primary files