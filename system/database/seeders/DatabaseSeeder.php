<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            AcademicYearSeeder::class,
            MajorSeeder::class,
            ClassSeeder::class,
            SubjectSeeder::class,
            WaveSeeder::class,
            StudentSeeder::class,
            ScheduleSeeder::class,
            LmsSeeder::class,
            AssignmentSeeder::class,
            MaterialSeeder::class,
            GradeSeeder::class,
            AnnouncementSeeder::class,
            BlogSeeder::class,
            GallerySeeder::class,
            SliderSeeder::class,
            FinancialDataSeeder::class,
            StudentPaymentSeeder::class,
            MonthlyPaymentPostSeeder::class,
            CbtExamSeeder::class,
            BookSeeder::class,
            CanteenSeeder::class,
            CanteenUserSeeder::class,
            BkViolationSeeder::class,
            BkSeeder::class,
            ExtracurricularAndCurriculumSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
