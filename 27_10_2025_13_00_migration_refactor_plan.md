# Migration and Model Refactoring Plan

## Date: 27/10/2025, 13:00 PM

## Objective
To investigate existing database migrations for bugs, duplication, security vulnerabilities, and BCNF violations, and to refactor them along with model relationships to address identified weaknesses, missing features, scalability limits, and redundancy issues.

## Plan

### Phase 1: Initial Analysis and Migration Review

1.  **Review All Migrations (`database/migrations/`)**:
    *   **Security**:
        *   Identify tables storing sensitive information (e.g., `users` table for passwords).
        *   Check for `varchar(255)` columns used for passwords and flag them for application-level hashing.
    *   **Data Integrity & BCNF**:
        *   Examine `STUDENT_OF_COURSE` (if it exists, or similar pivot tables) and `ATTENDANCE` for potential data duplication or aggregation issues.
        *   Assess `CREDIT_SCORE` table for redundancy with `AcademicRecord` or `Enrollment` data.
        *   Review `department_head` table for potential simplification into a column on the `departments` table.
    *   **Missing Features**:
        *   Verify all relevant tables have `timestamps()` (`created_at`, `updated_at`).
        *   Identify tables that should implement `softDeletes()` (`deleted_at`).
        *   Check for appropriate indexing on frequently queried columns (e.g., foreign keys, `student_id + course_number` in `attendances`, `payment_date` in `payments`, `classroom_id + time_slot` in `class_schedules`).
        *   Note rigid `enum` types (e.g., `gender`, `role`) and consider alternatives like lookup tables or more flexible string fields.
    *   **Scalability**:
        *   Identify `int(11)` primary/foreign key columns and plan to convert them to `bigIncrements()` or `bigInteger()->unsigned()` for future-proofing.
    *   **Other Issues**:
        *   Review `onDelete('cascade')` clauses for potential unintended data loss (e.g., `department_head` or similar relationships).

### Phase 2: Implement Migration Changes

1.  **Security Enhancements**:
    *   **Passwords**: Ensure `users` table migration does not explicitly store plaintext passwords. (The application layer will handle hashing).
2.  **Timestamps and Soft Deletes**:
    *   Modify existing migrations to add `timestamps()` to all relevant tables (e.g., `students`, `courses`, `enrollments`, `departments`, `faculties`, `degrees`, `majors`, `instructors`, `credit_scores`, `contact_details`, `attendances`, `payments`, `programs`, `classrooms`, `academic_records`, `class_schedules`, `audit_logs`, `transaction_ledgers`, `login_histories`, `system_configs`, `academic_years`, `semesters`).
    *   Modify existing migrations to add `softDeletes()` to key tables (e.g., `students`, `courses`, `enrollments`, `instructors`, `departments`, `programs`, `faculties`, `degrees`, `majors`).
3.  **Indexing**:
    *   Add specific indexes to migrations for performance:
        *   `attendances`: `student_id`, `class_schedule_id`, `attendance_date`.
        *   `payments`: `student_id`, `payment_date`.
        *   `class_schedules`: `classroom_id`, `time_slot_id` (composite index).
        *   Other frequently queried foreign keys.
4.  **ID Type Conversion**:
    *   Update `int(11)` ID columns to `bigIncrements()` for primary keys and `bigInteger()->unsigned()` for foreign keys in all relevant migrations.
5.  **Redundancy and Optimization**:
    *   **`department_head`**: Refactor `database/migrations/2025_10_27_022930_create_department_head_table.php` to remove the `department_head` table and instead add an `instructor_id` column to the `departments` table, making it a foreign key to `instructors`.
    *   **`CREDIT_SCORE`**: Re-evaluate `database/migrations/2025_10_27_022737_create_credit_scores_table.php`. If `AcademicRecord` or `Enrollment` can sufficiently track marks/credits, consider merging or removing `credit_scores`. (Further investigation needed during model relationship phase).
    *   **`STUDENT_OF_COURSE` / `ATTENDANCE`**: Clarify the purpose of `STUDENT_OF_COURSE` (if it exists) and `ATTENDANCE`. Ensure no redundant data storage.

### Phase 3: Model Relationship Definition and Refinement

1.  **Define Model Relationships (`app/Models/`)**:
    *   **`User`**: Define relationships (e.g., `hasOne` `ContactDetail`, `hasMany` `LoginHistory`, `belongsTo` `Department`).
    *   **`Department`**: Define relationships (e.g., `hasMany` `Programs`, `hasMany` `Courses`, `belongsTo` `Instructor` for head).
    *   **`Instructor`**: Define relationships (e.g., `hasMany` `Courses`, `hasMany` `ClassSchedules`, `hasOne` `ContactDetail`, `belongsTo` `Department`).
    *   **`Student`**: Define relationships (e.g., `hasOne` `ContactDetail`, `hasMany` `Enrollments`, `hasMany` `AcademicRecords`, `hasMany` `Payments`, `hasMany` `Attendances`, `belongsTo` `Program`, `belongsTo` `Gender`).
    *   **`Course`**: Define relationships (e.g., `belongsTo` `Department`, `belongsTo` `Semester`, `hasMany` `Enrollments`, `hasMany` `CoursePrerequisites`).
    *   **`Enrollment`**: Define relationships (e.g., `belongsTo` `Student`, `belongsTo` `Course`, `belongsTo` `ClassSchedule`).
    *   **`ClassSchedule`**: Define relationships (e.g., `belongsTo` `Course`, `belongsTo` `Instructor`, `belongsTo` `Classroom`, `belongsTo` `Semester`).
    *   **Many-to-Many for Instructors and Courses**: Create a pivot table (e.g., `course_instructor`) and define `belongsToMany` relationships if a course can have multiple instructors.
    *   **Other Models**: Define relationships for `AcademicRecord`, `AcademicYear`, `Attendance`, `AuditLog`, `Classroom`, `ContactDetail`, `CoursePrerequisite`, `CreditScore`, `Degree`, `Faculty`, `Gender`, `LoginHistory`, `Major`, `Payment`, `Program`, `Role`, `Semester`, `SystemConfig`, `TransactionLedger`.

### Phase 4: Validation and Testing (Conceptual)

1.  **Run Migrations**: Execute `php artisan migrate:fresh --seed` (or similar) in a development environment.
2.  **Test Relationships**: Verify that all defined model relationships work as expected using Eloquent.
3.  **Data Seeding**: Ensure seeders are updated to reflect new schema changes and relationships.

## Task Progress

- [ ] Create a plan in a markdown file.
- [ ] Review all existing migration files for issues.
- [ ] Implement security enhancements in migrations (password storage notes).
- [ ] Add `timestamps()` to all relevant tables in migrations.
- [ ] Implement `softDeletes()` on key tables in migrations.
- [ ] Add necessary indexes to migrations for performance.
- [ ] Convert `int(11)` IDs to `bigint(20) unsigned` in migrations.
- [ ] Refactor `department_head` table into a column on `departments` table.
- [ ] Re-evaluate `CREDIT_SCORE` table for redundancy.
- [ ] Define all model relationships in `app/Models/`.
- [ ] Create many-to-many pivot table for `Course` and `Instructor` if needed.
- [ ] (Conceptual) Run migrations and test relationships.
