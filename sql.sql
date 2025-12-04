// --- ACADEMIC STRUCTURE ---

Table academic_years {
  id integer [primary key, increment]
  name varchar
  start_date date
  end_date date
  is_current boolean
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table semesters {
  id integer [primary key, increment]
  academic_year_id integer [ref: > academic_years.id]
  name varchar
  start_date date
  end_date date
  is_active boolean
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table faculties {
  id integer [primary key, increment]
  name varchar
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table departments {
  id integer [primary key, increment]
  name varchar
  code varchar
  description text
  hod_id integer [note: 'Head of Department (User or Instructor ID)']
  faculty_id integer [ref: > faculties.id]
  email varchar
  phone varchar
  office_location varchar
  established_year integer
  budget decimal
  is_active boolean
  metadata json
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
  instructor_id integer [ref: > instructors.id]
}

Table programs {
  id integer [primary key, increment]
  degree_id integer [ref: > degrees.id]
  major_id integer [ref: > majors.id]
  name varchar
  is_active boolean
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table degrees {
  id integer [primary key, increment]
  name varchar
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table majors {
  id integer [primary key, increment]
  name varchar
  department_id integer [ref: > departments.id]
  degree_id integer [ref: > degrees.id]
  cost decimal
  payment_frequency varchar
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

// --- PEOPLE & USERS ---

Table users {
  id integer [primary key, increment]
  name varchar
  username varchar
  email varchar
  profile_pic varchar
  bio text
  email_verified_at timestamp
  password varchar
  last_login_at timestamp
  is_active boolean
  remember_token varchar
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
  department_id integer [ref: > departments.id]
}

Table students {
  id integer [primary key, increment]
  user_id integer [ref: - users.id]
  department_id integer [ref: > departments.id]
  program_id integer [ref: > programs.id]
  student_id varchar [note: 'University Roll Number']
  registration_number varchar
  gender_id integer [ref: > genders.id]
  date_of_birth date
  nationality varchar
  id_card_number varchar
  passport_number varchar
  phone varchar
  emergency_contact_name varchar
  emergency_contact_phone varchar
  emergency_contact_relation varchar
  current_address text
  city varchar
  district varchar
  commune varchar
  village varchar
  postal_code varchar
  admission_date date
  current_semester integer
  cgpa decimal
  total_credits_earned integer
  academic_status varchar
  enrollment_status varchar
  year_level integer
  semester integer
  fee_category varchar
  has_outstanding_balance boolean
  previous_education text
  blood_group varchar
  has_disability boolean
  disability_details text
  metadata json
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table instructors {
  id integer [primary key, increment]
  user_id integer [ref: - users.id]
  faculty_id integer [ref: > faculties.id]
  department_id integer [ref: > departments.id]
  payscale varchar
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table contact_details {
  person_id integer
  email varchar
  address text
  phone_number varchar
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table genders {
  id integer [primary key, increment]
  name varchar
  created_at timestamp
  updated_at timestamp
}

// --- COURSES & ACADEMIC ACTIVITY ---

Table courses {
  id integer [primary key, increment]
  name varchar
  code varchar
  faculty_id integer [ref: > faculties.id]
  semester_id integer [ref: > semesters.id]
  description text
  credits integer
  department_id integer [ref: > departments.id]
  program_id integer [ref: > programs.id]
  instructor_id integer [ref: > instructors.id]
  max_students integer
  start_date date
  end_date date
  status varchar
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table course_instructor_pivot {
  id integer [primary key, increment]
  course_id integer [ref: > courses.id]
  instructor_id integer [ref: > instructors.id]
  created_at timestamp
  updated_at timestamp
}

Table course_prerequisites {
  id integer [primary key, increment]
  course_id integer [ref: > courses.id]
  prerequisite_id integer [ref: > courses.id]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table class_schedules {
  id integer [primary key, increment]
  course_id integer [ref: > courses.id]
  instructor_id integer [ref: > instructors.id]
  classroom_id integer [ref: > classrooms.id]
  semester_id integer [ref: > semesters.id]
  capacity integer
  day_of_week varchar
  schedule_date date
  start_time time
  end_time time
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table enrollments {
  id integer [primary key, increment]
  student_id integer [ref: > students.id]
  semester_id integer [ref: > semesters.id]
  class_schedule_id integer [ref: > class_schedules.id]
  status varchar
  enrollment_date date
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table academic_records {
  id integer [primary key, increment]
  student_id integer [ref: > students.id]
  course_id integer [ref: > courses.id]
  semester_id integer [ref: > semesters.id]
  academic_year_id integer [ref: > academic_years.id]
  grade varchar
  marks decimal
  status varchar
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table credit_scores {
  id integer [primary key, increment]
  student_id integer [ref: > students.id]
  course_id integer [ref: > courses.id]
  score decimal
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table attendances {
  attendance_id integer [primary key, increment]
  student_id integer [ref: > students.id]
  class_schedule_id integer [ref: > class_schedules.id]
  date date
  status varchar [note: 'Present, Absent, Late']
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

// --- FACILITIES & FINANCE ---

Table classrooms {
  id integer [primary key, increment]
  room_number varchar
  type varchar
  building_name varchar
  capacity integer
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table payments {
  id integer [primary key, increment]
  student_id integer [ref: > students.id]
  academic_year_id integer [ref: > academic_years.id]
  amount decimal
  payment_date date
  payment_period_description text
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table transaction_ledgers {
  id integer [primary key, increment]
  user_id integer [ref: > users.id]
  transaction_type varchar
  debit decimal
  credit decimal
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

// --- SYSTEM & ROLES (Spatie) ---

Table roles {
  id integer [primary key, increment]
  name varchar
  guard_name varchar
  description text
  is_system_role boolean
  created_at timestamp
  updated_at timestamp
}

Table permissions {
  id integer [primary key, increment]
  name varchar
  guard_name varchar
  group varchar
  description text
  created_at timestamp
  updated_at timestamp
}

Table model_has_roles {
  role_id integer [ref: > roles.id]
  model_type varchar
  model_id integer
}

Table model_has_permissions {
  permission_id integer [ref: > permissions.id]
  model_type varchar
  model_id integer
}

Table role_has_permissions {
  permission_id integer [ref: > permissions.id]
  role_id integer [ref: > roles.id]
}

Table system_configs {
  id integer [primary key, increment]
  key varchar
  value text
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table audit_logs {
  id integer [primary key, increment]
  user_id integer [ref: > users.id]
  action varchar
  description text
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

Table login_histories {
  id integer [primary key, increment]
  user_id integer [ref: > users.id]
  ip_address varchar
  user_agent text
  login_at timestamp
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp
}

// --- LARAVEL INTERNAL TABLES ---

Table failed_jobs {
  id integer [primary key]
  uuid varchar
  connection text
  queue text
  payload text
  exception text
  failed_at timestamp
}

Table jobs {
  id integer [primary key]
  queue varchar
  payload text
  attempts integer
  reserved_at integer
  available_at integer
  created_at integer
}

Ref: "attendances"."created_at" < "attendances"."deleted_at"

Ref: "semesters"."id" < "semesters"."start_date"

Ref: "attendances"."attendance_id" < "attendances"."class_schedule_id"