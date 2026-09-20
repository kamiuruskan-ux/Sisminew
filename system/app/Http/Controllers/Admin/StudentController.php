<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Major;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StudentController extends Controller

{
    public function index(Request $request)
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();

        $classesQuery = ClassModel::where('is_active', true)->orderBy('name');
        if ($allowedClassIds !== null) {
            $classesQuery->whereIn('id', $allowedClassIds);
        }
        $classes = $classesQuery->get();
        $majors = Major::where('is_active', true)->orderBy('name')->get();

        $query = Student::with(['user', 'class', 'major']);

        if ($allowedClassIds !== null) {
            $query->whereIn('class_id', $allowedClassIds);
        }

        if ($request->filled('class_id')) {
            if ($request->class_id === 'none' || $request->class_id === 'null') {
                $query->whereNull('class_id');
            } else {
                if ($allowedClassIds === null || in_array($request->class_id, $allowedClassIds)) {
                    $query->where('class_id', $request->class_id);
                }
            }
        }

        if ($request->filled('major_id')) {
            if ($request->major_id === 'none' || $request->major_id === 'null') {
                $query->whereNull('major_id');
            } else {
                $query->where('major_id', $request->major_id);
            }
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Sort filter (Nama A-Z, Z-A, Terbaru)
        $sort = $request->input('sort', 'latest');
        if ($sort === 'name_asc') {
            $query->join('users as u_sort', 'students.user_id', '=', 'u_sort.id')
                  ->select('students.*')
                  ->orderBy('u_sort.name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->join('users as u_sort', 'students.user_id', '=', 'u_sort.id')
                  ->select('students.*')
                  ->orderBy('u_sort.name', 'desc');
        } else {
            $query->orderBy('students.created_at', 'desc');
        }

        // Per page: 10, 20, 50, 100 (default: 10)
        $perPage = in_array((int)$request->input('per_page'), [10, 20, 50, 100]) ? (int)$request->input('per_page') : 10;
        $students = $query->paginate($perPage)->withQueryString();

        $statsQuery = Student::query();
        if ($allowedClassIds !== null) {
            $statsQuery->whereIn('class_id', $allowedClassIds);
        }

        $totalStudents = (clone $statsQuery)->count();
        $maleCount = (clone $statsQuery)->where('gender', 'male')->count();
        $femaleCount = (clone $statsQuery)->where('gender', 'female')->count();

        return view('admin.students.index', compact(
            'students',
            'classes',
            'majors',
            'totalStudents',
            'maleCount',
            'femaleCount'
        ));
    }

    public function create()
    {
        $classes = ClassModel::where('is_active', true)->get();
        $majors = Major::where('is_active', true)->get();
        return view('admin.students.create', compact('classes', 'majors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'nis' => 'nullable|string|max:30|unique:students,nis',
            'nisn' => 'nullable|string|max:20|unique:students,nisn',
            'nik' => 'nullable|string|max:20|unique:students,nik',
            'gender' => 'required|in:male,female',
            'religion' => 'nullable|string|max:50',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'student_status' => 'nullable|string|max:50',
            'entry_year' => 'nullable|string|max:10',
            'parent_name' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:30',
            'parent_job' => 'nullable|string|max:100',
            'parent_address' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'major_id' => 'nullable|exists:majors,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'spp_discount' => 'nullable|numeric|min:0',
            'discount_description' => 'nullable|string|max:255',
            'pin' => 'nullable|numeric|digits:6',
        ], [
            'name.required' => 'Nama lengkap siswa wajib diisi.',
            'name.max' => 'Nama siswa tidak boleh melebihi 255 karakter.',
            'email.required' => 'Alamat email siswa wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan oleh pengguna lain.',
            'password.required' => 'Password akun siswa wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'gender.required' => 'Jenis kelamin siswa wajib dipilih.',
            'gender.in' => 'Jenis kelamin harus Laki-laki atau Perempuan.',
            'nis.unique' => 'Nomor Induk Siswa (NIS) ini sudah terdaftar pada siswa lain.',
            'nisn.unique' => 'NISN ini sudah terdaftar pada siswa lain.',
            'nik.unique' => 'NIK ini sudah terdaftar pada siswa lain.',
            'class_id.exists' => 'Kelas yang dipilih tidak valid.',
            'major_id.exists' => 'Jurusan yang dipilih tidak valid.',
            'photo.image' => 'Pasfoto siswa harus berupa berkas gambar (JPG, JPEG, PNG, WEBP).',
            'photo.mimes' => 'Format foto siswa harus JPG, JPEG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran file pasfoto tidak boleh melebihi 2MB.',
            'pin.digits' => 'PIN transaksi/absen siswa harus 6 digit angka.',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
            'spp_discount.numeric' => 'Potongan SPP harus berupa nominal angka.',
            'spp_discount.min' => 'Potongan SPP tidak boleh bernilai negatif.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
        ]);

        $studentRole = Role::where('slug', 'student')->first();
        if ($studentRole) {
            $user->assignRole($studentRole);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $savedPhoto = save_uploaded_public_file($request->file('photo'), 'img/students');
            $photoPath = basename($savedPhoto);
        }

        Student::create([
            'user_id' => $user->id,
            'nis' => $validated['nis'] ?? null,
            'nisn' => $validated['nisn'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'gender' => $validated['gender'],
            'religion' => $validated['religion'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'student_status' => $validated['student_status'] ?? 'active',
            'entry_year' => $validated['entry_year'] ?? null,
            'parent_name' => $validated['parent_name'] ?? null,
            'father_name' => $validated['father_name'] ?? null,
            'mother_name' => $validated['mother_name'] ?? null,
            'parent_phone' => $validated['parent_phone'] ?? null,
            'parent_job' => $validated['parent_job'] ?? null,
            'parent_address' => $validated['parent_address'] ?? null,
            'class_id' => $validated['class_id'] ?? null,
            'major_id' => $validated['major_id'] ?? null,
            'photo' => $photoPath,
            'spp_discount' => $validated['spp_discount'] ?? 0,
            'discount_description' => $validated['discount_description'] ?? null,
            'pin' => !empty($validated['pin']) ? Hash::make($validated['pin']) : null,
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show($encodedId)
    {
        $id = is_numeric($encodedId) ? $encodedId : decode_id($encodedId);
        $student = Student::with(['user', 'class', 'major'])->findOrFail($id);

        $allowedClassIds = auth()->user()->getAssignedClassIds();
        if ($allowedClassIds !== null && (!in_array($student->class_id, $allowedClassIds))) {
            abort(403, 'Akses ditolak: Anda hanya dapat melihat santri pada kelas yang ditugaskan kepada Anda.');
        }

        return view('admin.students.show', compact('student'));
    }

    public function edit($encodedId)
    {
        $id = decode_id($encodedId);
        $student = Student::with(['user', 'class', 'major'])->findOrFail($id);

        $allowedClassIds = auth()->user()->getAssignedClassIds();
        if ($allowedClassIds !== null && (!in_array($student->class_id, $allowedClassIds))) {
            abort(403, 'Akses ditolak: Anda hanya dapat mengedit santri pada kelas yang ditugaskan kepada Anda.');
        }

        $classesQuery = ClassModel::where('is_active', true)->orderBy('name');
        if ($allowedClassIds !== null) {
            $classesQuery->whereIn('id', $allowedClassIds);
        }
        $classes = $classesQuery->get();
        $majors = Major::where('is_active', true)->get();

        return view('admin.students.edit', compact('student', 'classes', 'majors'));
    }

    public function update(Request $request, $encodedId)
    {
        $id = decode_id($encodedId);
        $student = Student::findOrFail($id);

        $allowedClassIds = auth()->user()->getAssignedClassIds();
        if ($allowedClassIds !== null && (!in_array($student->class_id, $allowedClassIds))) {
            abort(403, 'Akses ditolak: Anda hanya dapat memperbarui data santri pada kelas yang ditugaskan kepada Anda.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $student->user_id,
            'password' => 'nullable|string|min:8',
            'nis' => 'nullable|string|max:30|unique:students,nis,' . $student->id,
            'nisn' => 'nullable|string|max:20|unique:students,nisn,' . $student->id,
            'nik' => 'nullable|string|max:20|unique:students,nik,' . $student->id,
            'gender' => 'required|in:male,female',
            'religion' => 'nullable|string|max:50',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'student_status' => 'nullable|string|max:50',
            'entry_year' => 'nullable|string|max:10',
            'parent_name' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:30',
            'parent_job' => 'nullable|string|max:100',
            'parent_address' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'major_id' => 'nullable|exists:majors,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'spp_discount' => 'nullable|numeric|min:0',
            'discount_description' => 'nullable|string|max:255',
            'pin' => 'nullable|numeric|digits:6',
        ], [
            'name.required' => 'Nama lengkap siswa wajib diisi.',
            'name.max' => 'Nama siswa tidak boleh melebihi 255 karakter.',
            'email.required' => 'Alamat email siswa wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan oleh pengguna lain.',
            'password.min' => 'Password baru minimal terdiri dari 8 karakter.',
            'gender.required' => 'Jenis kelamin siswa wajib dipilih.',
            'gender.in' => 'Jenis kelamin harus Laki-laki atau Perempuan.',
            'nis.unique' => 'Nomor Induk Siswa (NIS) ini sudah terdaftar pada siswa lain.',
            'nisn.unique' => 'NISN ini sudah terdaftar pada siswa lain.',
            'nik.unique' => 'NIK ini sudah terdaftar pada siswa lain.',
            'class_id.exists' => 'Kelas yang dipilih tidak valid.',
            'major_id.exists' => 'Jurusan yang dipilih tidak valid.',
            'photo.image' => 'Pasfoto siswa harus berupa berkas gambar (JPG, JPEG, PNG, WEBP).',
            'photo.mimes' => 'Format foto siswa harus JPG, JPEG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran file pasfoto tidak boleh melebihi 2MB.',
            'pin.digits' => 'PIN transaksi/absen siswa harus 6 digit angka.',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
            'spp_discount.numeric' => 'Potongan SPP harus berupa nominal angka.',
            'spp_discount.min' => 'Potongan SPP tidak boleh bernilai negatif.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $student->user->update($userData);

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                delete_public_file($student->photo, 'img/students');
            }
            $savedPhoto = save_uploaded_public_file($request->file('photo'), 'img/students');
            $validated['photo'] = 'students/' . basename($savedPhoto);
        } elseif ($request->input('remove_photo') === '1' || $request->boolean('remove_photo')) {
            if ($student->photo) {
                delete_public_file($student->photo, 'img/students');
            }
            $validated['photo'] = null;
        } else {
            unset($validated['photo']);
        }

        if ($request->filled('pin')) {
            $validated['pin'] = Hash::make($request->pin);
        } else {
            unset($validated['pin']);
        }

        $student->update($validated);

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy($encodedId)
    {
        // Jika input berupa angka murni (misal: 10), gunakan langsung. Jika string hash, decode dulu.
        $id = is_numeric($encodedId) ? $encodedId : decode_id($encodedId);
        $student = Student::findOrFail($id);

        $allowedClassIds = auth()->user()->getAssignedClassIds();
        if ($allowedClassIds !== null && (!in_array($student->class_id, $allowedClassIds))) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk menghapus santri di luar kelas binaan Anda.');
        }
        
        // Hapus file foto jika ada
        if ($student->photo) {
            delete_public_file($student->photo, 'img/students');
        }

        // Hapus relasi user jika ada
        if ($student->user) {
            $student->user->delete();
        }

        // Hapus data siswa
        $student->delete();

        return back()->with('success', 'Siswa berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required',
        ]);

        $ids = collect($request->ids)->map(function ($id) {
            return is_numeric($id) ? (int)$id : decode_id($id);
        })->filter();

        $allowedClassIds = auth()->user()->getAssignedClassIds();
        $query = Student::whereIn('id', $ids);
        if ($allowedClassIds !== null) {
            $query->whereIn('class_id', $allowedClassIds);
        }
        $students = $query->get();

        $count = 0;
        foreach ($students as $student) {
            if ($student->photo) {
                delete_public_file($student->photo, 'img/students');
            }
            if ($student->user) {
                $student->user->delete();
            }
            $student->delete();
            $count++;
        }

        return back()->with('success', "Berhasil menghapus {$count} data siswa terpilih.");
    }

    public function bulkEdit(Request $request)
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();

        $classesQuery = ClassModel::where('is_active', true)->orderBy('name');
        if ($allowedClassIds !== null) {
            $classesQuery->whereIn('id', $allowedClassIds);
        }
        $classes = $classesQuery->get();
        $majors = Major::where('is_active', true)->orderBy('name')->get();

        $query = Student::with(['user', 'class', 'major']);

        if ($allowedClassIds !== null) {
            $query->whereIn('class_id', $allowedClassIds);
        }

        if ($request->has('ids') && is_array($request->ids)) {
            $decodedIds = collect($request->ids)->map(function ($id) {
                return is_numeric($id) ? (int)$id : decode_id($id);
            })->filter();

            if ($decodedIds->isNotEmpty()) {
                $query->whereIn('id', $decodedIds);
            }
        }

        if ($request->filled('class_id')) {
            if ($request->class_id === 'none' || $request->class_id === 'null') {
                $query->whereNull('class_id');
            } else {
                $query->where('class_id', $request->class_id);
            }
        }

        if ($request->filled('major_id')) {
            if ($request->major_id === 'none' || $request->major_id === 'null') {
                $query->whereNull('major_id');
            } else {
                $query->where('major_id', $request->major_id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('class_id')->orderBy('nisn')->paginate(100)->withQueryString();

        return view('admin.students.bulk-edit', compact(
            'students',
            'classes',
            'majors'
        ));
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'students' => 'required|array',
            'students.*.id' => 'required',
            'students.*.class_id' => 'nullable|exists:classes,id',
            'students.*.major_id' => 'nullable|exists:majors,id',
            'selected_ids' => 'nullable|array',
        ]);

        $selectedIds = $request->input('selected_ids', []);

        DB::transaction(function () use ($validated, $selectedIds) {
            foreach ($validated['students'] as $studentData) {
                $studentId = is_numeric($studentData['id']) ? $studentData['id'] : decode_id($studentData['id']);
                
                // Jika selected_ids diisi, hanya update siswa yang dicentang
                if (!empty($selectedIds) && !in_array($studentId, $selectedIds) && !in_array((string)$studentData['id'], $selectedIds)) {
                    continue;
                }

                $student = Student::find($studentId);
                if ($student) {
                    $updateData = [];
                    if (array_key_exists('class_id', $studentData)) {
                        $updateData['class_id'] = $studentData['class_id'] ?: null;
                    }
                    if (array_key_exists('major_id', $studentData)) {
                        $updateData['major_id'] = $studentData['major_id'] ?: null;
                    }
                    if (!empty($updateData)) {
                        $student->update($updateData);
                    }
                }
            }
        });

        $count = !empty($selectedIds) ? count($selectedIds) : count($validated['students']);
        $label = Setting::get('is_vocational', '1') == '1' ? 'Kelas dan Jurusan' : 'Kelas';

        return redirect()->route('admin.students.index')->with('success', "Berhasil memperbarui data {$label} untuk {$count} siswa.");
    }


    /**
     * Print student ID card with QR code
     */
    public function printCard($encodedId)
    {
        $student = Student::with(['user', 'class', 'major'])->findOrFail(decode_id($encodedId));

        $allowedClassIds = auth()->user()->getAssignedClassIds();
        if ($allowedClassIds !== null && (!in_array($student->class_id, $allowedClassIds))) {
            abort(403, 'Akses ditolak: Anda tidak memiliki hak akses mencetak kartu santri di luar kelas binaan Anda.');
        }
        
        // Generate QR code data (Pakai NISN saja)
        $qrData = $student->nisn ?? $student->user->email;

        // Generate QR code as SVG
        $qrCode = QrCode::size(200)->generate($qrData);

        return view('admin.students.print-card', compact('student', 'qrCode'));
    }

    /**
     * Export Student Data as Excel (.xlsx)
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();

        $query = Student::with(['user', 'class', 'major']);
        if ($allowedClassIds !== null) {
            $query->whereIn('class_id', $allowedClassIds);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('major_id')) {
            $query->where('major_id', $request->major_id);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Headers
        $headers = [
            'A1' => 'NISN',
            'B1' => 'NIK',
            'C1' => 'Nama Lengkap',
            'D1' => 'Email',
            'E1' => 'No HP / WA',
            'F1' => 'Jenis Kelamin',
            'G1' => 'Kelas',
            'H1' => 'Jurusan',
            'I1' => 'Tempat Lahir',
            'J1' => 'Tanggal Lahir',
            'K1' => 'Alamat',
            'L1' => 'Nama Wali',
            'M1' => 'No HP Wali',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style Header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3C50E0']
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $row = 2;
        foreach ($students as $student) {
            $sheet->setCellValueExplicit('A' . $row, $student->nisn ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('B' . $row, $student->nik ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $student->user->name ?? '');
            $sheet->setCellValue('D' . $row, $student->user->email ?? '');
            $sheet->setCellValueExplicit('E' . $row, $student->phone ?? $student->user->phone ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('F' . $row, $student->gender === 'male' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('G' . $row, $student->class->name ?? '');
            $sheet->setCellValue('H' . $row, $student->major->name ?? '');
            $sheet->setCellValue('I' . $row, $student->birth_place ?? '');
            $sheet->setCellValue('J' . $row, $student->birth_date ? $student->birth_date->format('Y-m-d') : '');
            $sheet->setCellValue('K' . $row, $student->address ?? '');
            $sheet->setCellValue('L' . $row, $student->parent_name ?? '');
            $sheet->setCellValueExplicit('M' . $row, $student->parent_phone ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $row++;
        }

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Data_Siswa_Sekolah_' . date('Y-m-d_H-i') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Download Import Template Excel (.xlsx)
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Siswa');

        $headers = [
            'A1' => 'nisn',
            'B1' => 'nik',
            'C1' => 'nama_lengkap',
            'D1' => 'email',
            'E1' => 'password',
            'F1' => 'no_hp',
            'G1' => 'jenis_kelamin',
            'H1' => 'kelas',
            'I1' => 'jurusan',
            'J1' => 'tempat_lahir',
            'K1' => 'tanggal_lahir',
            'L1' => 'alamat',
            'M1' => 'nama_wali',
            'N1' => 'no_hp_wali',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style Header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Sample data row 1
        $sheet->setCellValueExplicit('A2', '0012345678', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B2', '3201234567890001', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C2', 'Ahmad Fauzi');
        $sheet->setCellValue('D2', 'ahmad.fauzi@example.com');
        $sheet->setCellValue('E2', 'password123');
        $sheet->setCellValueExplicit('F2', '081234567890', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('G2', 'Laki-laki');
        $sheet->setCellValue('H2', 'X IPA 1');
        $sheet->setCellValue('I2', 'Teknik Komputer dan Jaringan');
        $sheet->setCellValue('J2', 'Jakarta');
        $sheet->setCellValue('K2', '2008-05-15');
        $sheet->setCellValue('L2', 'Jl. Merdeka No. 10');
        $sheet->setCellValue('M2', 'Budi Santoso');
        $sheet->setCellValueExplicit('N2', '081298765432', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        // Sample data row 2
        $sheet->setCellValueExplicit('A3', '0098765432', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B3', '3201234567890002', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('C3', 'Siti Nurhaliza');
        $sheet->setCellValue('D3', 'siti.nurhaliza@example.com');
        $sheet->setCellValue('E3', 'password123');
        $sheet->setCellValueExplicit('F3', '085712345678', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('G3', 'Perempuan');
        $sheet->setCellValue('H3', 'X IPA 2');
        $sheet->setCellValue('I3', 'Rekayasa Perangkat Lunak');
        $sheet->setCellValue('J3', 'Bandung');
        $sheet->setCellValue('K3', '2008-08-20');
        $sheet->setCellValue('L3', 'Jl. Mawar No. 5');
        $sheet->setCellValue('M3', 'Ahmad Dahlan');
        $sheet->setCellValueExplicit('N3', '085798765432', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Template_Import_Siswa.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Import Student Data from Excel (.xlsx, .xls, .csv)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, false, true); // Raw values to handle dates and formulas properly
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal membaca berkas Excel/CSV: ' . $e->getMessage()], 422);
            }
            return back()->with('error', 'Gagal membaca berkas Excel/CSV.');
        }

        if (empty($data) || count($data) < 2) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Berkas Excel/CSV kosong atau tidak berisi data.'], 422);
            }
            return back()->with('error', 'Berkas Excel/CSV kosong atau tidak berisi data.');
        }

        // Definisi alias nama kolom yang sering digunakan di Indonesia / format sekolah
        $columnAliases = [
            'nisn'          => ['nisn', 'no_nisn', 'nomor induk siswa nasional', 'nisn_siswa', 'nis/nisn', 'nis_nisn'],
            'nik'           => ['nik', 'no_nik', 'nik_siswa', 'nomor induk kependudukan', 'no ktp', 'no kk', 'nik / no kitas'],
            'name'          => ['nama_lengkap', 'nama', 'nama_siswa', 'nama siswa', 'name', 'student_name', 'nama peserta didik', 'peserta didik', 'nama santri'],
            'email'         => ['email', 'e-mail', 'surel', 'alamat email', 'email_siswa'],
            'password'      => ['password', 'kata sandi', 'pass', 'sandi', 'pin'],
            'phone'         => ['no_hp', 'no hp', 'nohp', 'telepon', 'no telepon', 'no telp', 'phone', 'hp', 'no wa', 'whatsapp', 'no_whatsapp'],
            'gender'        => ['jenis_kelamin', 'jenis kelamin', 'jk', 'gender', 'sex', 'l/p', 'l_p'],
            'class'         => ['kelas', 'class', 'rombel', 'nama kelas', 'tingkat', 'kelas_id'],
            'major'         => ['jurusan', 'major', 'program keahlian', 'kompetensi keahlian', 'keahlian', 'konsentrasi keahlian'],
            'birth_place'   => ['tempat_lahir', 'tempat lahir', 'kota lahir', 'birth_place', 'tempat_lahir_siswa'],
            'birth_date'    => ['tanggal_lahir', 'tanggal lahir', 'tgl lahir', 'tgl_lahir', 'birth_date', 'birthdate'],
            'address'       => ['alamat', 'alamat tinggal', 'alamat siswa', 'address', 'domisili'],
            'parent_name'   => ['nama_wali', 'nama wali', 'wali', 'orang tua', 'nama orang tua', 'ayah', 'ibu', 'nama ayah', 'nama ibu', 'parent_name'],
            'parent_phone'  => ['no_hp_wali', 'no hp wali', 'nohp wali', 'kontak wali', 'telepon wali', 'parent_phone', 'no wa orang tua', 'hp_wali'],
        ];

        // Normalizer string
        $normalize = function ($str) {
            $str = strtolower(trim((string)$str));
            return preg_replace('/[^a-z0-9]/', '', $str);
        };

        // Cari baris header secara cerdas (paling banyak cocok dalam 5 baris pertama)
        $headerRowIndex = null;
        $matchedColMap = []; // field => column letter ('A', 'B', ...)
        $rowsArray = array_values($data);

        for ($i = 0; $i < min(6, count($rowsArray)); $i++) {
            $rowCandidate = $rowsArray[$i];
            $currentMatches = [];

            foreach ($rowCandidate as $colLetter => $cellVal) {
                $cellNorm = $normalize($cellVal);
                if (empty($cellNorm)) continue;

                foreach ($columnAliases as $field => $aliases) {
                    if (isset($currentMatches[$field])) continue;
                    foreach ($aliases as $alias) {
                        if ($cellNorm === $normalize($alias)) {
                            $currentMatches[$field] = $colLetter;
                            break;
                        }
                    }
                }
            }

            // Jika baris ini memiliki kecocokan nama atau nisn atau setidaknya 2 field
            if (isset($currentMatches['name']) || isset($currentMatches['nisn']) || count($currentMatches) >= 2) {
                if (count($currentMatches) > count($matchedColMap)) {
                    $matchedColMap = $currentMatches;
                    $headerRowIndex = $i;
                }
            }
        }

        // Potong baris hingga setelah header
        if ($headerRowIndex !== null) {
            $dataRows = array_slice($rowsArray, $headerRowIndex + 1);
        } else {
            // Fallback jika tidak ada baris header yang cocok sama sekali, gunakan urutan default template
            $dataRows = array_slice($rowsArray, 1);
            $matchedColMap = [
                'nisn'         => 'A',
                'nik'          => 'B',
                'name'         => 'C',
                'email'        => 'D',
                'password'     => 'E',
                'phone'        => 'F',
                'gender'       => 'G',
                'class'        => 'H',
                'major'        => 'I',
                'birth_place'  => 'J',
                'birth_date'   => 'K',
                'address'      => 'L',
                'parent_name'  => 'M',
                'parent_phone' => 'N',
            ];
        }

        $classes = ClassModel::all();
        $majors = Major::all();
        $studentRole = Role::where('slug', 'student')->first();

        $imported = 0;
        $createdCount = 0;
        $updatedCount = 0;
        $skipped = 0;
        $logs = [];
        $lineNum = ($headerRowIndex !== null ? $headerRowIndex + 1 : 1);

        foreach ($dataRows as $row) {
            $lineNum++;
            if (empty(array_filter($row, function($v) { return $v !== null && trim((string)$v) !== ''; }))) {
                continue; // Lewati baris kosong total
            }

            $getVal = function ($field) use ($row, $matchedColMap) {
                $col = $matchedColMap[$field] ?? null;
                if ($col && isset($row[$col])) {
                    $val = trim((string)$row[$col]);
                    return $val !== '' ? $val : null;
                }
                return null;
            };

            $nisn        = $getVal('nisn');
            $nik         = $getVal('nik');
            $name        = $getVal('name');
            $email       = $getVal('email');
            $pass        = $getVal('password') ?: 'password123';
            $phone       = $getVal('phone');
            $genderRaw   = strtolower((string)($getVal('gender') ?? ''));
            $className   = $getVal('class');
            $majorName   = $getVal('major');
            $birthPlace  = $getVal('birth_place');
            $birthDateRaw = $getVal('birth_date');
            $address     = $getVal('address');
            $parentName  = $getVal('parent_name');
            $parentPhone = $getVal('parent_phone');

            // Validasi nama wajib ada
            if (empty($name)) {
                $skipped++;
                $logs[] = "[SKIP] Baris {$lineNum}: Nama siswa kosong.";
                continue;
            }

            // Normalisasi Email
            if (!empty($email)) {
                $email = strtolower(trim($email));
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email = null;
                }
            }

            // Normalisasi Gender
            $gender = 'male';
            if (in_array($genderRaw, ['female', 'perempuan', 'p', 'f', 'wanita'])) {
                $gender = 'female';
            } elseif (in_array($genderRaw, ['male', 'laki-laki', 'laki - laki', 'laki', 'l', 'pria', 'm'])) {
                $gender = 'male';
            }

            // Normalisasi Tanggal Lahir (Excel serial number atau string tanggal)
            $birthDate = null;
            if (!empty($birthDateRaw)) {
                if (is_numeric($birthDateRaw) && $birthDateRaw > 1000) {
                    try {
                        $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$birthDateRaw);
                        $birthDate = $dt->format('Y-m-d');
                    } catch (\Exception $ex) {
                        $birthDate = null;
                    }
                } else {
                    $cleanDateStr = str_replace('/', '-', trim($birthDateRaw));
                    $parsedTime = strtotime($cleanDateStr);
                    if ($parsedTime !== false && $parsedTime > 0) {
                        $birthDate = date('Y-m-d', $parsedTime);
                    }
                }
            }

            // Pencocokan Rombel Kelas
            $classId = null;
            if ($className) {
                $cleanSearchClass = $normalize($className);
                $matchedClass = $classes->first(function ($c) use ($className, $cleanSearchClass, $normalize) {
                    return strcasecmp($c->name, $className) === 0 
                        || $c->id == $className 
                        || $normalize($c->name) === $cleanSearchClass;
                });
                if ($matchedClass) {
                    $classId = $matchedClass->id;
                }
            }

            // Pencocokan Program Keahlian / Jurusan
            $majorId = null;
            if ($majorName) {
                $cleanSearchMajor = $normalize($majorName);
                $matchedMajor = $majors->first(function ($m) use ($majorName, $cleanSearchMajor, $normalize) {
                    return strcasecmp($m->name, $majorName) === 0 
                        || $m->id == $majorName 
                        || $normalize($m->name) === $cleanSearchMajor;
                });
                if ($matchedMajor) {
                    $majorId = $matchedMajor->id;
                }
            }

            // Cari apakah Siswa sudah pernah ada di DB (berdasarkan NISN atau Email User)
            $existingStudent = null;
            if (!empty($nisn)) {
                $existingStudent = Student::where('nisn', $nisn)->first();
            }
            if (!$existingStudent && !empty($email)) {
                $existingUser = User::where('email', $email)->first();
                if ($existingUser && $existingUser->student) {
                    $existingStudent = $existingUser->student;
                }
            }

            // Eksekusi Simpan (UPSERT: Update jika ada, Create jika baru)
            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                if ($existingStudent) {
                    // Mode UPDATE Data Siswa Eksisting
                    $user = $existingStudent->user;
                    if ($user) {
                        $userPayload = ['name' => $name];
                        if ($phone) $userPayload['phone'] = $phone;
                        if (!empty($email) && $email !== $user->email && !User::where('email', $email)->where('id', '!=', $user->id)->exists()) {
                            $userPayload['email'] = $email;
                        }
                        if ($pass && $pass !== 'password123') {
                            $userPayload['password'] = Hash::make($pass);
                        }
                        $user->update($userPayload);
                    }

                    $existingStudent->update([
                        'nisn'         => $nisn ?: $existingStudent->nisn,
                        'nik'          => $nik ?: $existingStudent->nik,
                        'gender'       => $gender,
                        'birth_place'  => $birthPlace ?: $existingStudent->birth_place,
                        'birth_date'   => $birthDate ?: $existingStudent->birth_date,
                        'phone'        => $phone ?: $existingStudent->phone,
                        'address'      => $address ?: $existingStudent->address,
                        'parent_name'  => $parentName ?: $existingStudent->parent_name,
                        'parent_phone' => $parentPhone ?: $existingStudent->parent_phone,
                        'class_id'     => $classId ?: $existingStudent->class_id,
                        'major_id'     => $majorId ?: $existingStudent->major_id,
                    ]);

                    \Illuminate\Support\Facades\DB::commit();

                    $imported++;
                    $updatedCount++;
                    $classInfo = ($classId && $className) ? " ({$className})" : "";
                    $logs[] = "[PASS] Baris {$lineNum}: {$name}{$classInfo} - Berhasil diperbarui (Updated).";
                } else {
                    // Mode CREATE Siswa Baru
                    if (empty($email)) {
                        $baseEmail = null;
                        if (!empty($nisn)) {
                            $cleanNisn = preg_replace('/[^0-9]/', '', $nisn);
                            if ($cleanNisn) {
                                $baseEmail = $cleanNisn . '@siswa.sekolah.id';
                            }
                        }
                        if (!$baseEmail) {
                            $cleanName = Str::slug($name, '');
                            $baseEmail = ($cleanName ? substr($cleanName, 0, 15) : 'siswa') . '_' . rand(100, 999) . '@siswa.sekolah.id';
                        }

                        $genEmail = $baseEmail;
                        $counter = 1;
                        while (User::where('email', $genEmail)->exists()) {
                            $parts = explode('@', $baseEmail);
                            $genEmail = $parts[0] . $counter . '@' . ($parts[1] ?? 'siswa.sekolah.id');
                            $counter++;
                        }
                        $email = $genEmail;
                    }

                    $existingUser = User::where('email', $email)->first();
                    if ($existingUser) {
                        $user = $existingUser;
                        $user->update([
                            'name'     => $name,
                            'phone'    => $phone ?: $user->phone,
                            'password' => Hash::make($pass),
                        ]);
                    } else {
                        $user = User::create([
                            'name'     => $name,
                            'email'    => $email,
                            'password' => Hash::make($pass),
                            'phone'    => $phone,
                        ]);
                    }

                    if ($studentRole && !$user->hasRole($studentRole->slug)) {
                        $user->assignRole($studentRole);
                    }

                    Student::create([
                        'user_id'      => $user->id,
                        'nisn'         => $nisn,
                        'nik'          => $nik,
                        'gender'       => $gender,
                        'birth_place'  => $birthPlace,
                        'birth_date'   => $birthDate,
                        'phone'        => $phone,
                        'address'      => $address,
                        'parent_name'  => $parentName,
                        'parent_phone' => $parentPhone,
                        'class_id'     => $classId,
                        'major_id'     => $majorId,
                    ]);

                    \Illuminate\Support\Facades\DB::commit();

                    $imported++;
                    $createdCount++;
                    $classInfo = ($classId && $className) ? " ({$className})" : "";
                    $logs[] = "[PASS] Baris {$lineNum}: {$name}{$classInfo} - Berhasil dibuat baru (Created).";
                }
            } catch (\Illuminate\Database\QueryException $qe) {
                \Illuminate\Support\Facades\DB::rollBack();
                $skipped++;
                if ($qe->errorInfo[1] == 1062) {
                    $logs[] = "[SKIP] Baris {$lineNum}: {$name} -> Data duplikat di database.";
                } else {
                    $logs[] = "[FAIL] Baris {$lineNum}: {$name} -> " . $qe->getMessage();
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                $skipped++;
                $logs[] = "[FAIL] Baris {$lineNum}: {$name} -> " . $e->getMessage();
            }
        }

        $msg = "Import Excel selesai! {$imported} data siswa diproses ({$createdCount} baru, {$updatedCount} di-update), {$skipped} dilewati.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => $msg,
                'total'    => $imported + $skipped,
                'imported' => $imported,
                'skipped'  => $skipped,
                'logs'     => $logs,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Display directory & management page for Student Face ID
     */
    public function faceIdIndex(Request $request)
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();

        $classesQuery = ClassModel::where('is_active', true)->orderBy('name');
        if ($allowedClassIds !== null) {
            $classesQuery->whereIn('id', $allowedClassIds);
        }
        $classes = $classesQuery->get();

        $selectedClassId = $request->input('class_id');
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Student::with(['user', 'class', 'major']);

        if ($allowedClassIds !== null) {
            $query->whereIn('class_id', $allowedClassIds);
        }

        if ($selectedClassId) {
            if ($allowedClassIds === null || in_array($selectedClassId, $allowedClassIds)) {
                $query->where('class_id', $selectedClassId);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($status === 'registered') {
            $query->whereHas('user', function ($q) {
                $q->whereNotNull('face_photo');
            });
        } elseif ($status === 'unregistered') {
            $query->where(function ($q) {
                $q->whereDoesntHave('user')
                  ->orWhereHas('user', function ($qu) {
                      $qu->whereNull('face_photo');
                  });
            });
        }

        $students = $query->orderBy('class_id')->orderBy('id', 'desc')->paginate(18)->withQueryString();

        // Calculate summary statistics
        $baseStatsQuery = Student::query();
        if ($allowedClassIds !== null) {
            $baseStatsQuery->whereIn('class_id', $allowedClassIds);
        }

        $totalStudents = (clone $baseStatsQuery)->count();
        $registeredCount = (clone $baseStatsQuery)->whereHas('user', fn($q) => $q->whereNotNull('face_photo'))->count();
        $unregisteredCount = $totalStudents - $registeredCount;
        $registrationPercentage = $totalStudents > 0 ? round(($registeredCount / $totalStudents) * 100, 1) : 0;

        // List of all students for modal search select
        $selectStudentsQuery = Student::with(['user', 'class'])->orderBy('id', 'desc');
        if ($allowedClassIds !== null) {
            $selectStudentsQuery->whereIn('class_id', $allowedClassIds);
        }
        $allStudentsForSelect = $selectStudentsQuery->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->user?->name ?? $s->nisn ?? 'Siswa #' . $s->id,
                    'nisn' => $s->nisn ? 'NISN: ' . $s->nisn : '',
                    'class' => $s->class?->name ?? 'Tanpa Kelas',
                    'is_registered' => !empty($s->user?->face_photo),
                    'face_photo_url' => $s->user?->face_photo ? asset('img/face_id/' . $s->user->face_photo) : null,
                ];
            });

        return view('admin.students.face-id', compact(
            'students',
            'classes',
            'selectedClassId',
            'search',
            'status',
            'totalStudents',
            'registeredCount',
            'unregisteredCount',
            'registrationPercentage',
            'allStudentsForSelect'
        ));
    }

    /**
     * Store or Update Student Face ID
     */
    public function storeFaceId(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'face_photo' => 'required|string',
        ]);

        $student = Student::with('user')->findOrFail($request->student_id);

        $allowedClassIds = auth()->user()->getAssignedClassIds();
        if ($allowedClassIds !== null && (!in_array($student->class_id, $allowedClassIds))) {
            abort(403, 'Akses ditolak: Anda hanya dapat mendaftarkan Face ID santri pada kelas yang ditugaskan kepada Anda.');
        }

        $user = $student->user;

        if (!$user) {
            $email = $student->email ?: ($student->nisn ? $student->nisn . '@sekolah.id' : 'student_' . $student->id . '@sekolah.id');
            $user = User::create([
                'name' => $student->name,
                'email' => $email,
                'password' => Hash::make($student->nisn ?: '12345678'),
                'phone' => $student->phone,
            ]);
            $student->user_id = $user->id;
            $student->save();
        }

        $imageData = $request->face_photo;
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]);
            $imageData = base64_decode($imageData);

            if ($imageData !== false) {
                $fileName = 'student_face_' . $student->id . '_' . time() . '.' . $type;
                $targetDir = public_path('img/face_id');
                if (!File::isDirectory($targetDir)) {
                    File::makeDirectory($targetDir, 0755, true, true);
                }

                // Delete old face photo
                if ($user->face_photo) {
                    if (File::exists(public_path('img/face_id/' . $user->face_photo))) {
                        File::delete(public_path('img/face_id/' . $user->face_photo));
                    }
                    if (File::exists(public_path('uploads/face_id/' . $user->face_photo))) {
                        File::delete(public_path('uploads/face_id/' . $user->face_photo));
                    }
                }

                File::put($targetDir . '/' . $fileName, $imageData);
                $user->face_photo = $fileName;
            }
        }

        if ($request->filled('face_embedding')) {
            $user->face_embedding = is_array($request->face_embedding) ? json_encode($request->face_embedding) : $request->face_embedding;
        }

        $user->face_registered_at = now();
        $user->save();

        $photoUrl = asset('img/face_id/' . $user->face_photo);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Face ID Siswa ' . $user->name . ' berhasil disimpan!',
                'face_photo_url' => $photoUrl,
                'registered_at' => $user->face_registered_at->format('d M Y H:i'),
            ]);
        }

        return back()->with('success', 'Face ID Siswa ' . $user->name . ' berhasil disimpan.');
    }

    /**
     * Delete Student Face ID
     */
    public function destroyFaceId($id)
    {
        $student = Student::with('user')->findOrFail($id);

        $allowedClassIds = auth()->user()->getAssignedClassIds();
        if ($allowedClassIds !== null && (!in_array($student->class_id, $allowedClassIds))) {
            abort(403, 'Akses ditolak: Anda hanya dapat menghapus Face ID santri pada kelas yang ditugaskan kepada Anda.');
        }

        if ($student->user) {
            $user = $student->user;
            if ($user->face_photo) {
                if (File::exists(public_path('img/face_id/' . $user->face_photo))) {
                    File::delete(public_path('img/face_id/' . $user->face_photo));
                }
                if (File::exists(public_path('uploads/face_id/' . $user->face_photo))) {
                    File::delete(public_path('uploads/face_id/' . $user->face_photo));
                }
            }
            $user->face_photo = null;
            $user->face_embedding = null;
            $user->face_registered_at = null;
            $user->save();
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Face ID Siswa ' . $student->name . ' berhasil dihapus.'
            ]);
        }

        return back()->with('success', 'Face ID Siswa ' . $student->name . ' berhasil dihapus.');
    }

    /**
     * Toggle Student Account Status (Active/Inactive) & Unlock Bruteforce Lockout
     */
    public function toggleStatus(Request $request, $encodedId)
    {
        $id = is_numeric($encodedId) ? $encodedId : decode_id($encodedId);
        $student = Student::with('user')->findOrFail($id);

        $user = $student->user;
        if (!$user) {
            $email = $student->email ?: ($student->nisn ? $student->nisn . '@sekolah.id' : 'student_' . $student->id . '@sekolah.id');
            $user = User::create([
                'name' => $student->name,
                'email' => $email,
                'password' => Hash::make($student->nisn ?: '12345678'),
                'phone' => $student->phone,
                'status' => 'active',
            ]);
            $student->user_id = $user->id;
            $student->save();
        }

        if ($request->has('status')) {
            $newStatus = $request->status ? 'active' : 'inactive';
        } else {
            $newStatus = ($user->status === 'active' && !$user->isLockedOut()) ? 'inactive' : 'active';
        }

        $user->status = $newStatus;
        $student->student_status = $newStatus;

        if ($newStatus === 'active') {
            $user->failed_login_attempts = 0;
            $user->locked_until = null;
        }

        $user->save();
        $student->save();

        $isCurrentlyActive = ($user->status === 'active' && !$user->isLockedOut());
        $statusLabel = $isCurrentlyActive ? 'Aktif' : 'Nonaktif / Terkunci';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Status akun siswa {$student->name} berhasil diubah menjadi {$statusLabel}.",
                'status' => $user->status,
                'is_locked' => $user->isLockedOut(),
            ]);
        }

        return back()->with('success', "Status akun siswa {$student->name} berhasil diubah menjadi {$statusLabel}.");
    }
}

