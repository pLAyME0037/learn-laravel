<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Header -->
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">
            {{ __('Academic Dashboard') }}
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            <!-- === LEFT COLUMN (Main Board & Widgets) === -->
            <div class="lg:col-span-3 space-y-6">

                <!-- 1. Big Board (Hero Section) -->
                <x-academic.hero-section :student="$student" />

                <!-- 2. Stats Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-academic.stat-card title="GPA"
                        value="{{ $student->cgpa ?? '0.00' }}"
                        icon="academic-cap"
                        color="indigo" />

                    <x-academic.stat-card title="Credits Earned"
                        value="{{ $student->total_credits_earned ?? 0 }}"
                        icon="check-badge"
                        color="emerald" />

                    <x-academic.stat-card title="Status"
                        value="{{ ucfirst($student->academic_status ?? 'N/A') }}"
                        icon="user"
                        color="blue" />
                </div>

                <!-- 3. Bottom Section: Charts & Progress -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Progress Chart -->
                    <x-academic.semester-progress :percent="$semesterProgress" />

                    <!-- Activity Feed -->
                    <x-academic.activity-feed :activities="$activities" />
                </div>

            </div>

            <!-- === RIGHT COLUMN (Sidebar Links) === -->
            <div class="lg:col-span-1">
                <x-academic.sidebar-links :links="$quickLinks" />
            </div>

        </div>
    </div>
</div>
