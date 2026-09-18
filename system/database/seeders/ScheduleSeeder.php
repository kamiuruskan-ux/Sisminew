<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schedule::query()->delete();

        $classes = ClassModel::all();

        if ($classes->isEmpty()) {
            return;
        }

        $schedules = [
            'Monday' => [
                ['name' => 'Matematika Wajib', 'subject' => 'Matematika', 'start_time' => '07:30', 'end_time' => '09:00', 'room' => 'Ruang 101', 'teacher' => 'Dra. Hidayati, M.Pd.'],
                ['name' => 'Bahasa Indonesia', 'subject' => 'Bahasa Indonesia', 'start_time' => '09:15', 'end_time' => '10:45', 'room' => 'Ruang 101', 'teacher' => 'Budi Santoso, S.Pd.'],
                ['name' => 'Fisika Dasar', 'subject' => 'Fisika', 'start_time' => '11:00', 'end_time' => '12:30', 'room' => 'Lab IPA 1', 'teacher' => 'John Teacher'],
                ['name' => 'Pendidikan Agama & Budi Pekerti', 'subject' => 'Pendidikan Agama', 'start_time' => '13:15', 'end_time' => '14:45', 'room' => 'Ruang 101', 'teacher' => 'Ust. Ahmad Dahlan, S.Ag.'],
            ],
            'Tuesday' => [
                ['name' => 'Bahasa Inggris', 'subject' => 'Bahasa Inggris', 'start_time' => '07:30', 'end_time' => '09:00', 'room' => 'Lab Bahasa', 'teacher' => 'Siti Aminah, M.Hum.'],
                ['name' => 'Kimia Organik', 'subject' => 'Kimia', 'start_time' => '09:15', 'end_time' => '10:45', 'room' => 'Lab Kimia', 'teacher' => 'Dr. Bambang Sugiarto'],
                ['name' => 'Informatika & Pemrograman', 'subject' => 'Informatika', 'start_time' => '11:00', 'end_time' => '12:30', 'room' => 'Lab Komputer 1', 'teacher' => 'Ahmad Risyad, S.Kom.'],
                ['name' => 'Sejarah Indonesia', 'subject' => 'Sejarah', 'start_time' => '13:15', 'end_time' => '14:45', 'room' => 'Ruang 101', 'teacher' => 'Dra. Endang Lestari'],
            ],
            'Wednesday' => [
                ['name' => 'Biologi Sel & Molekuler', 'subject' => 'Biologi', 'start_time' => '07:30', 'end_time' => '09:00', 'room' => 'Lab Biologi', 'teacher' => 'Nurmala, M.Si.'],
                ['name' => 'Matematika Peminatan', 'subject' => 'Matematika', 'start_time' => '09:15', 'end_time' => '10:45', 'room' => 'Ruang 101', 'teacher' => 'Dra. Hidayati, M.Pd.'],
                ['name' => 'Pendidikan Pancasila & Kewarganegaraan', 'subject' => 'PPKn', 'start_time' => '11:00', 'end_time' => '12:30', 'room' => 'Ruang 101', 'teacher' => 'Drs. Supriyanto'],
                ['name' => 'Seni Budaya & Prakarya', 'subject' => 'Seni Budaya', 'start_time' => '13:15', 'end_time' => '14:45', 'room' => 'Ruang Kesenian', 'teacher' => 'Dewi Safitri, S.Sn.'],
            ],
            'Thursday' => [
                ['name' => 'Pendidikan Jasmani, Olahraga & Kesehatan', 'subject' => 'PJOK', 'start_time' => '07:30', 'end_time' => '09:30', 'room' => 'Lapangan Olahraga', 'teacher' => 'Rahmat Hidayat, S.Pd.'],
                ['name' => 'Fisika Lanjutan', 'subject' => 'Fisika', 'start_time' => '09:45', 'end_time' => '11:15', 'room' => 'Lab IPA 1', 'teacher' => 'John Teacher'],
                ['name' => 'Bahasa Inggris Lanjutan', 'subject' => 'Bahasa Inggris', 'start_time' => '11:15', 'end_time' => '12:45', 'room' => 'Lab Bahasa', 'teacher' => 'Siti Aminah, M.Hum.'],
                ['name' => 'Kewirausahaan & Prakarya', 'subject' => 'Kewirausahaan', 'start_time' => '13:15', 'end_time' => '14:45', 'room' => 'Ruang 101', 'teacher' => 'Dewi Lestari, M.M.'],
            ],
            'Friday' => [
                ['name' => 'Bimbingan Konseling & Character Building', 'subject' => 'BK', 'start_time' => '07:30', 'end_time' => '08:30', 'room' => 'Ruang 101', 'teacher' => 'Rina Kartika, S.Psi.'],
                ['name' => 'Informatika Praktikum', 'subject' => 'Informatika', 'start_time' => '08:30', 'end_time' => '10:00', 'room' => 'Lab Komputer 1', 'teacher' => 'Ahmad Risyad, S.Kom.'],
                ['name' => 'Matematika Pengayaan', 'subject' => 'Matematika', 'start_time' => '10:15', 'end_time' => '11:30', 'room' => 'Ruang 101', 'teacher' => 'Dra. Hidayati, M.Pd.'],
            ],
        ];

        foreach ($classes as $class) {
            foreach ($schedules as $day => $items) {
                foreach ($items as $item) {
                    Schedule::create([
                        'name' => $item['name'] . ' - ' . $class->name,
                        'subject' => $item['subject'],
                        'day' => $day,
                        'start_time' => $item['start_time'],
                        'end_time' => $item['end_time'],
                        'room' => $item['room'],
                        'teacher' => $item['teacher'],
                        'class_id' => $class->id,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
