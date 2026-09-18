<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class VerifyStudentPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only enforce PIN for student role
        if ($user && $user->hasRole('student')) {
            $student = $user->student;

            if ($student) {
                // List of routes exempt from PIN check
                $exemptRoutes = [
                    'student.pin.verify',
                    'student.pin.verify.submit',
                    'student.pin.set',
                    'student.pin.set.submit',
                    'logout',
                ];

                $currentRouteName = Route::currentRouteName();

                if (!in_array($currentRouteName, $exemptRoutes)) {
                    // Check if PIN has been set
                    if (empty($student->pin)) {
                        return redirect()->route('student.pin.set')
                            ->with('info', 'Untuk keamanan akun Anda, silakan buat PIN 6-digit terlebih dahulu.');
                    }

                    // Check if PIN has been verified in current session
                    if ($request->session()->get('student_pin_verified') !== true) {
                        return redirect()->route('student.pin.verify')
                            ->with('info', 'Silakan masukkan PIN keamanan Anda.');
                    }
                }
            }
        }

        return $next($request);
    }
}
