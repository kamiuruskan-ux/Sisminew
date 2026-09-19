/**
 * AttendanceService - Attendance Submission, Validation & Coordinator
 * Orchestrates GPS vs Fingerprint validation rules and backend synchronization.
 * 
 * Rules:
 * - If GPS: validates school radius geofence (bypassed if Dinas Luar with note).
 * - If Fingerprint: RADIUS CHECK IS BYPASSED completely. Valid even without GPS.
 * - Error handling: always specific and informative.
 */
(function (global) {
    'use strict';

    class AttendanceService {
        constructor() {
            this.endpoints = {
                gpsCheckin: '/admin/teacher-attendances/self-checkin',
                fingerprintVerify: '/admin/teacher-attendances/fingerprint/verify',
                statusCheck: '/admin/teacher-attendances/check-status'
            };
            this.csrfToken = '';
            this.userId = null;
            this.schoolConfig = {
                latitude: -0.8917,
                longitude: 119.8707,
                radius: 100,
                name: 'Sekolah'
            };
        }

        init(config = {}) {
            if (config.csrfToken) this.csrfToken = config.csrfToken;
            if (config.userId) this.userId = config.userId;
            if (config.school) {
                this.schoolConfig = Object.assign({}, this.schoolConfig, config.school);
            }

            global.AttendanceLogger?.attendance('AttendanceService initialized with config:', {
                userId: this.userId,
                school: this.schoolConfig
            });
        }

        setCsrfToken(token) {
            this.csrfToken = token;
        }

        /**
         * Submit Attendance via GPS (Mobile / Laptop Geofence)
         * @param {Object} options 
         * @returns {Promise<Object>}
         */
        async submitGpsAttendance(options = {}) {
            const mode = options.mode || 'reguler'; // 'reguler' | 'dinas_luar'
            const session = options.session || global.AttendanceScheduleService.resolveCurrentSession();
            const coords = options.coords || global.AttendanceGPSService.currentCoords;
            const dinasNotes = options.dinasNotes || '';

            global.AttendanceLogger?.attendance('Processing GPS Attendance Submission...', {
                mode,
                sessionType: session.type,
                coords
            });

            // 1. Validate Schedule Window
            if (session.type === 'outside_window') {
                return {
                    success: false,
                    message: 'Saat ini belum memasuki jadwal presensi aktif sekolah. Harap tunggu hingga sesi presensi dibuka.'
                };
            }

            if (session.is_already_done) {
                return {
                    success: false,
                    message: `Anda sudah menyelesaikan ${session.name} hari ini.`
                };
            }

            // 2. Validate Dinas Luar Requirements
            if (mode === 'dinas_luar') {
                if (!dinasNotes.trim()) {
                    return {
                        success: false,
                        message: 'Catatan atau nomor surat tugas dinas luar wajib diisi sebagai bukti penugasan.'
                    };
                }
            } else {
                // 3. Regular WFO Mode - Validate GPS Coordinates & Radius
                if (!coords || coords.latitude === null || coords.longitude === null) {
                    return {
                        success: false,
                        message: 'Browser belum mendapatkan koordinat GPS satelit yang akurat. Tekan tombol "Refresh GPS" untuk mendeteksi lokasi terkini.'
                    };
                }

                const radiusService = global.AttendanceRadiusService;
                const distance = radiusService.calculateDistance(
                    this.schoolConfig.latitude,
                    this.schoolConfig.longitude,
                    coords.latitude,
                    coords.longitude
                );

                const inRadius = radiusService.isWithinRadius(distance, this.schoolConfig.radius);

                if (!inRadius) {
                    const explanation = radiusService.getOutsideRadiusExplanation(
                        distance,
                        this.schoolConfig.radius,
                        this.schoolConfig.name
                    );

                    global.AttendanceLogger?.warn('ATTENDANCE', 'GPS Attendance rejected: Outside radius', {
                        distance,
                        maxRadius: this.schoolConfig.radius
                    });

                    return {
                        success: false,
                        outOfRadius: true,
                        distance,
                        message: explanation
                    };
                }
            }

            // 4. Send request to backend
            try {
                const payload = {
                    type: session.type,
                    latitude: coords ? coords.latitude : null,
                    longitude: coords ? coords.longitude : null,
                    accuracy: coords ? coords.accuracy : null,
                    attendance_mode: mode,
                    work_location: mode === 'dinas_luar' ? 'outstation' : 'school',
                    dinas_notes: dinasNotes,
                    notes: dinasNotes
                };

                global.AttendanceLogger?.attendance('Sending GPS checkin payload to backend:', payload);

                const res = await fetch(this.endpoints.gpsCheckin, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                global.AttendanceLogger?.attendance('Backend GPS checkin response received:', data);

                if (res.ok && data.success) {
                    return {
                        success: true,
                        message: data.message || 'Presensi berhasil diverifikasi dan tersimpan ke sistem.',
                        attendance: data.attendance || data.data
                    };
                } else {
                    return {
                        success: false,
                        message: data.message || 'Presensi tidak dapat disimpan oleh server.'
                    };
                }
            } catch (netErr) {
                global.AttendanceLogger?.error('ATTENDANCE', 'Network communication error during GPS attendance:', netErr);
                return {
                    success: false,
                    message: 'Terjadi kendala koneksi internet saat menghubungi server sekolah. Silakan periksa jaringan dan coba kembali.'
                };
            }
        }

        /**
         * Submit Attendance via Physical USB Fingerprint Scanner
         * NOTE: RADIUS IS EXPLICITLY NOT CHECKED for fingerprint attendance!
         * @param {string} sampleData Biometric template or raw sample string
         * @param {Object} options 
         * @returns {Promise<Object>}
         */
        async submitFingerprintAttendance(sampleData, options = {}) {
            global.AttendanceLogger?.fingerprint('Submitting Fingerprint Biometric to server...', {
                userId: this.userId,
                hasSample: Boolean(sampleData)
            });

            if (!sampleData) {
                return {
                    success: false,
                    message: 'Sampel sidik jari kosong atau kualitas pembacaan sensor tidak memadai. Silakan tempelkan jari kembali.'
                };
            }

            try {
                const payload = {
                    user_id: this.userId || options.userId || null,
                    fingerprint_sample: sampleData,
                    device_name: options.deviceName || 'HID DigitalPersona U.are.U 4500'
                };

                const res = await fetch(this.endpoints.fingerprintVerify, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                global.AttendanceLogger?.fingerprint('Backend Fingerprint response:', data);

                if (res.ok && data.success) {
                    return {
                        success: true,
                        message: data.message || 'Sidik jari berhasil diverifikasi! Presensi Anda telah dicatat.',
                        attendance: data.attendance || data.data,
                        user: data.user
                    };
                } else {
                    let msg = data.message || 'Sidik jari tidak cocok dengan rekaman sistem biometrik.';
                    if (data.locked) {
                        msg = data.message || 'Presensi untuk sesi ini sudah pernah dilakukan.';
                    }
                    return {
                        success: false,
                        message: msg,
                        confidence: data.confidence || 0
                    };
                }
            } catch (netErr) {
                global.AttendanceLogger?.error('FINGERPRINT', 'Network error during fingerprint verification:', netErr);
                return {
                    success: false,
                    message: 'Gagal menghubungkan server presensi sekolah saat memverifikasi sidik jari. Silakan coba kembali.'
                };
            }
        }
    }

    global.AttendanceUnifiedService = new AttendanceService();
})(typeof window !== 'undefined' ? window : this);
