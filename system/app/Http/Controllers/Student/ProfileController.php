<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student as ModelsStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $student = $user->student;
        $photoDoc = $student ? $student->spmbRegistration()->first()?->documents()->where('type', 'photo')->first() : null;

        $qrData = $student?->nisn ?? $user->email;
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrData);

        return view('student.profile', compact('user', 'student', 'photoDoc', 'qrCode'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $student = ModelsStudent::firstOrCreate(['user_id' => $user->id]);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|file|max:10240',
        ];

        if ($request->filled('nisn')) {
            $rules['nisn'] = ['nullable', 'string', 'max:20', \Illuminate\Validation\Rule::unique('students', 'nisn')->ignore($student->id)];
        }
        if ($request->filled('nik')) {
            $rules['nik'] = ['nullable', 'string', 'max:20', \Illuminate\Validation\Rule::unique('students', 'nik')->ignore($student->id)];
        }

        $validated = $request->validate($rules);

        // Update user name and email
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Prepare student updates
        $studentData = [
            'email' => $validated['email'],
        ];

        $studentFields = ['nisn', 'nik', 'phone', 'gender', 'birth_place', 'birth_date', 'address', 'parent_name', 'parent_phone'];
        foreach ($studentFields as $field) {
            if ($request->has($field)) {
                $studentData[$field] = $request->input($field);
            }
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $ext = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif', 'svg'];
            
            if (!in_array($ext, $allowedExtensions)) {
                return back()->withErrors(['photo' => 'Format foto harus berupa JPG, JPEG, PNG, GIF, WEBP, atau JFIF.']);
            }

            if ($student->photo) {
                delete_public_file('img/students/' . basename($student->photo));
            }
            $savedPhoto = save_uploaded_public_file($file, 'img/students');
            $studentData['photo'] = basename($savedPhoto);
        } elseif ($request->input('remove_photo') === '1' || $request->boolean('remove_photo')) {
            if ($student->photo) {
                delete_public_file('img/students/' . basename($student->photo));
            }
            $studentData['photo'] = null;
        }

        $student->update($studentData);

        return back()->with('success', 'Profil siswa berhasil diperbarui.');
    }

    public function account()
    {
        $user = auth()->user();
        $student = $user->student;
        $photoDoc = $student ? $student->spmbRegistration()->first()?->documents()->where('type', 'photo')->first() : null;
        
        return view('student.account', compact('user', 'student', 'photoDoc'));
    }

    public function updateAccount(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required_with:new_password|min:8',
            'new_password' => 'required_with:current_password|min:8|confirmed',
        ]);

        if (!empty($validated['current_password'])) {
            if (Hash::check($validated['current_password'], $user->password)) {
                $user->password = Hash::make($validated['new_password']);
                $user->save();
                return back()->with('success', 'Password berhasil diubah.');
            } else {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
        }

        return back()->withErrors(['current_password' => 'Password saat ini diperlukan untuk mengubah password.']);
    }

    public function updatePin(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return back()->withErrors(['pin' => 'Data siswa tidak ditemukan.']);
        }

        $hasPin = !empty($student->pin);

        $rules = [
            'pin' => 'required|numeric|digits:6|confirmed',
        ];

        if ($hasPin) {
            $rules['current_pin'] = 'required|numeric|digits:6';
        } else {
            $rules['current_password'] = 'required|string';
        }

        $validated = $request->validate($rules, [
            'pin.required' => 'PIN baru wajib diisi.',
            'pin.numeric' => 'PIN harus berupa angka.',
            'pin.digits' => 'PIN harus terdiri dari 6 digit.',
            'pin.confirmed' => 'Konfirmasi PIN tidak cocok.',
            'current_pin.required' => 'PIN saat ini wajib diisi.',
            'current_pin.digits' => 'PIN saat ini harus 6 digit.',
            'current_password.required' => 'Password saat ini diperlukan untuk membuat PIN.',
        ]);

        if ($hasPin) {
            if (!Hash::check($validated['current_pin'], $student->pin)) {
                return back()->withErrors(['current_pin' => 'PIN saat ini salah.']);
            }
        } else {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password salah.']);
            }
        }

        $student->pin = Hash::make($validated['pin']);
        $student->save();

        return back()->with('success', 'PIN keamanan berhasil diperbarui.');
    }

    public function showPinVerifyForm()
    {
        $user = auth()->user();
        $student = $user->student;

        // If student does not have a PIN, redirect to set PIN
        if (!$student || empty($student->pin)) {
            return redirect()->route('student.pin.set');
        }

        // If already verified, redirect to dashboard
        if (session('student_pin_verified') === true) {
            return redirect()->route('student.dashboard');
        }

        return view('student.pin-verify', compact('user', 'student'));
    }

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|numeric|digits:6',
        ], [
            'pin.required' => 'PIN wajib diisi.',
            'pin.numeric' => 'PIN harus berupa angka.',
            'pin.digits' => 'PIN harus terdiri dari 6 digit.',
        ]);

        $user = auth()->user();
        $student = $user->student;

        if (!$student || empty($student->pin)) {
            return redirect()->route('student.pin.set');
        }

        if (Hash::check($request->pin, $student->pin)) {
            session(['student_pin_verified' => true]);
            return redirect()->intended(route('student.dashboard'))->with('success', 'PIN berhasil diverifikasi. Selamat datang kembali!');
        }

        return back()->withErrors(['pin' => 'PIN keamanan yang Anda masukkan salah.']);
    }

    public function showPinSetForm()
    {
        $user = auth()->user();
        $student = $user->student;

        // If PIN is already set, redirect to verify or dashboard
        if ($student && !empty($student->pin)) {
            return redirect()->route('student.pin.verify');
        }

        return view('student.pin-set', compact('user', 'student'));
    }

    public function setPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|numeric|digits:6|confirmed',
            'password' => 'required|string',
        ], [
            'pin.required' => 'PIN baru wajib diisi.',
            'pin.numeric' => 'PIN harus berupa angka.',
            'pin.digits' => 'PIN harus terdiri dari 6 digit.',
            'pin.confirmed' => 'Konfirmasi PIN tidak cocok.',
            'password.required' => 'Password akun diperlukan untuk memverifikasi pembuatan PIN.',
        ]);

        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return back()->withErrors(['pin' => 'Data siswa tidak ditemukan.']);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password salah.']);
        }

        $student->pin = Hash::make($request->pin);
        $student->save();

        session(['student_pin_verified' => true]);

        return redirect()->route('student.dashboard')->with('success', 'PIN keamanan berhasil dibuat! Sekarang Anda dapat mengakses Dashboard.');
    }
}
