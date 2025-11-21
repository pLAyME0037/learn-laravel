# Schul SYS – Functional Workflow & Operational Documentation

This document outlines the operational logic, user journeys, and management workflows for the **Schul SYS** School Management System. It serves as a guide for understanding how different roles interact with the system and the value proposition for each stakeholder.

---

## 1. The Ecosystem Overview
The system acts as the central nervous system for the educational institution. It bridges the gap between **Academic Operations** (learning, grading) and **Administrative Operations** (finance, HR, facilities).

**Core Philosophy:** "Data entered once, accessible everywhere."

---

## 2. The Student Journey (User Point of View)

The student is the primary consumer of the system. Their workflow moves from **Admission** to **Graduation**.

### Phase A: Admission & Onboarding
1.  **Registration:** The student (or Registrar) creates a profile. The system generates a unique `Student ID` and links them to a `User` account for login.
2.  **Program Assignment:** The student is assigned a **Department** (e.g., Computer Science) and a **Program** (e.g., Bachelor of Software Engineering).
3.  **Profile Completion:** Student updates emergency contacts, addresses, and uploads a profile picture.

### Phase B: Semester Enrollment (The "Start of Term" Loop)
*This happens every semester.*
1.  **Check Eligibility:** The system checks if the student has no financial holds and is "Active."
2.  **Course Selection:**
    *   Student views the **Class Schedule**.
    *   Student selects courses.
    *   **System Validation:**
        *   Does the student meet **Prerequisites**?
        *   Is there a **Time Conflict** with another class?
        *   Is the **Classroom Capacity** full?
3.  **Confirmation:** Once approved, an `Enrollment` record is created. The student now appears on the Instructor's attendance sheet.

### Phase C: Daily Academic Life
1.  **Dashboard:** Student logs in to see their schedule ("What class do I have today?"), announcements, and upcoming exams.
2.  **Attendance:** They can view their attendance percentage (e.g., "I have 85% attendance in Math").
3.  **Resources:** Download assignments or lecture notes uploaded by the instructor.

### Phase D: Financials
1.  **Billing:** The system generates a tuition invoice based on the number of credits taken.
2.  **Payment:** Student/Parent views outstanding balance. Payments are recorded (Cash/Online), and the balance updates instantly.

### Phase E: Assessment & Progression
1.  **Results:** After exams, students view their **Grades** and **GPA/CGPA** (automatically calculated by the system).
2.  **Transcript:** At any point, they can request/view an unofficial transcript showing their academic history.

---

## 3. The Instructor Workflow (Faculty Point of View)

The instructor is the service provider. Their goal is efficient classroom management and assessment.

### Phase A: Course Assignment
1.  **Allocation:** The Admin assigns the Instructor to specific **Courses** and **Class Schedules** (e.g., "Intro to AI" on Mondays at 10:00 AM).
2.  **Roster:** The instructor logs in and sees the list of enrolled students for each class.

### Phase B: Classroom Management
1.  **Attendance Taking:**
    *   Instructor opens the "Attendance" module.
    *   Selects the specific class session.
    *   Marks students as *Present, Absent, Late,* or *Excused*.
    *   *System Action:* Updates the student's attendance stats immediately.
2.  **Content Delivery:** Uploading syllabus, assignment guidelines, or resources to the specific Course page.

### Phase C: Grading & Assessment (Crucial)
1.  **Input Marks:**
    *   Instructor enters scores for Quizzes, Midterms, and Finals.
    *   The system weighs these components (e.g., Final is 40%) to calculate the total score.
2.  **Final Grade Submission:**
    *   Instructor submits the final Letter Grade (A, B, C).
    *   **System Action:** Updates the student's `AcademicRecord` and recalculates the student's CGPA.

---

## 4. The Administration Hub (Management Point of View)

Admins are the "Architects." They set the rules and maintain the infrastructure.

### Phase A: System Configuration (The Setup)
Before any student can enroll, the Admin must:
1.  **Academic Years & Semesters:** Define the timeline (e.g., "Fall 2025").
2.  **Physical Infrastructure:** Create `Buildings` and `Classrooms` (defining capacity to prevent overcrowding).
3.  **Academic Structure:**
    *   Create `Faculties` -> `Departments` -> `Programs`.
    *   Create `Courses` (Subject matter) and `Course Prerequisites`.

### Phase B: Scheduling (The Matrix)
This is the most complex Admin task.
1.  **Create Class Schedule:**
    *   Select a **Course** (Intro to AI).
    *   Select an **Instructor** (Dr. Smith).
    *   Select a **Room** (Room 101).
    *   Select a **Time Slot** (Mon 10 AM).
    *   **System Check:** Ensure neither the Room nor the Instructor is double-booked.

### Phase C: User & Role Management (Security)
1.  **RBAC (Role-Based Access Control):**
    *   Admin defines `Roles` (e.g., "Registrar", "Finance Officer").
    *   Admin assigns `Permissions` (e.g., `view.payments`, `create.students`).
2.  **Audit:** Monitoring `AuditLogs` to see who changed a grade or deleted a record.

---

## 5. Detailed Functional Logic

### How Enrollment Logic Works
1.  **Input:** Student selects `ClassSchedule ID: 50`.
2.  **Check 1 (Prereq):** Does Student have a passing grade in the prerequisite course? -> *If No, Block.*
3.  **Check 2 (Capacity):** Count existing enrollments for ID 50. Is it < Room Capacity? -> *If No, Block.*
4.  **Check 3 (Time):** Does Student have another class at the same time? -> *If Yes, Block.*
5.  **Success:** Create `Enrollment` record. Decrease available seats.

### How GPA Calculation Works
1.  **Trigger:** Instructor submits a grade.
2.  **Process:**
    *   Convert Letter Grade to Point (A = 4.0).
    *   Multiply Point by Course Credits (4.0 * 3 credits = 12 Quality Points).
    *   Sum all Quality Points / Sum all Credits Attempted.
3.  **Output:** Update `students.cgpa` column.

---

## 6. Value Proposition: What do they get back?

### 🎓 For the Student
*   **Clarity:** No more guessing regarding schedules or grades.
*   **History:** A permanent digital record of their achievements.
*   **Convenience:** Ability to enroll and pay from anywhere.

### 👨‍🏫 For the Instructor
*   **Automation:** No manual GPA calculations.
*   **Organization:** Instant access to student lists and attendance history.
*   **Communication:** Streamlined way to reach all students in a class.

### 🏢 For the Administration
*   **Control:** Granular permission settings ensure data security.
*   **Integrity:** Audit logs prevent fraud (grade changing).
*   **Insight:** Reports on which programs are popular, which students are at risk (low attendance/GPA), and financial health.

---

## 7. Summary of Responsibilities

| Feature | Managed By | Used By |
| :--- | :--- | :--- |
| **User Accounts** | Admin | Everyone |
| **Course Creation** | Admin / Head of Dept | - |
| **Class Scheduling** | Registrar / Admin | Students & Instructors |
| **Enrollment** | Student (Self-Service) | Admin (Oversees) |
| **Attendance** | Instructor | Student (Views) |
| **Grades** | Instructor | Student (Views) |
| **Payments** | Finance Officer | Student (Pays) |
| **System Config** | Super Admin | - |