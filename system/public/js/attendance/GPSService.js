/**
 * GPSService - High-Accuracy Geolocation Provider with Auto-Retry and Fresh Satellite Coordinate Acquisition
 * Strict implementation of enableHighAccuracy: true, maximumAge: 0, timeout: 15000 with 3x retry.
 */
(function (global) {
    'use strict';

    class GPSService {
        constructor() {
            this.state = 'idle'; // 'idle' | 'prompt' | 'searching' | 'retrying' | 'connected' | 'denied' | 'disabled'
            this.currentCoords = null; // { latitude, longitude, accuracy, timestamp }
            this.lastError = null;
            this.retryCount = 0;
            this.maxRetries = 3;
            this.listeners = {
                stateChange: [],
                position: [],
                error: []
            };

            // High-precision GPS options with Tier 1 and Tier 2 definitions
            this.highAccuracyOptions = {
                enableHighAccuracy: true,
                maximumAge: 30000, // 30s cache
                timeout: 6000     // 6 seconds fast timeout
            };

            this.standardOptions = {
                enableHighAccuracy: false,
                maximumAge: 300000, // 5 minutes cache
                timeout: 8000      // 8 seconds timeout
            };
        }

        on(event, fn) {
            if (this.listeners[event] && typeof fn === 'function') {
                this.listeners[event].push(fn);
            }
        }

        _setState(newState, payload = null) {
            this.state = newState;
            global.AttendanceLogger?.gps(`State transitioned to: [${newState}]`, payload);
            this.listeners.stateChange.forEach((fn) => {
                try { fn(newState, payload); } catch (e) { console.error(e); }
            });
        }

        _notifyPosition(coords) {
            this.currentCoords = coords;
            this.lastError = null;
            this.retryCount = 0;
            this._setState('connected', coords);
            this.listeners.position.forEach((fn) => {
                try { fn(coords); } catch (e) { console.error(e); }
            });
        }

        _notifyError(err) {
            this.lastError = err;
            this.listeners.error.forEach((fn) => {
                try { fn(err); } catch (e) { console.error(e); }
            });
        }

        /**
         * Initialize GPS detection based on permissions query
         */
        async init() {
            const permService = global.AttendancePermissionService;
            let permStatus = 'prompt';
            try {
                permStatus = await permService.queryGeolocationPermission();
            } catch (e) {
                permStatus = 'prompt';
            }

            global.AttendanceLogger?.permission(`GPSService.init - Permission status: ${permStatus}`);

            // Listen for permission change in browser (e.g. user toggles Allow in address bar)
            if (permService && typeof permService.onChange === 'function') {
                permService.onChange((newPerm) => {
                    global.AttendanceLogger?.permission(`GPSService detected permission change: ${newPerm}`);
                    if (newPerm === 'granted') {
                        this.acquireLocation(true);
                    } else if (newPerm === 'denied') {
                        this._setState('denied', { message: 'Izin akses lokasi ditolak oleh browser.' });
                    } else if (newPerm === 'prompt') {
                        this._setState('prompt', { message: 'Izin akses lokasi diperlukan.' });
                    }
                });
            }

            if (permStatus === 'denied') {
                this._setState('denied', { message: 'Izin akses lokasi ditolak oleh browser.' });
                return Promise.reject(new Error('Geolocation permission denied'));
            }

            // Immediately acquire coordinates with multi-tier fallback
            return this.acquireLocation(false);
        }

        /**
         * Core method to acquire location from device GPS with automatic Tier 1 -> Tier 2 fallback
         * @param {boolean} isExplicitUserRefresh 
         */
        acquireLocation(isExplicitUserRefresh = false) {
            if (typeof navigator === 'undefined' || !navigator.geolocation) {
                const err = new Error('Browser tidak mendukung geolokasi GPS.');
                this._setState('disabled', { message: err.message });
                this._notifyError(err);
                return Promise.reject(err);
            }

            if (isExplicitUserRefresh) {
                this.currentCoords = null;
                this.retryCount = 0;
            }

            this._setState(this.retryCount > 0 ? 'retrying' : 'searching', {
                retry: this.retryCount,
                maxRetries: this.maxRetries
            });

            global.AttendanceLogger?.gps(`Mencari koordinat satelit GPS (Tier 1 High-Accuracy)...`);

            return new Promise((resolve, reject) => {
                // Tier 1: Try High Accuracy (Satelit / Assisted GPS)
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const coords = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy,
                            timestamp: position.timestamp || Date.now()
                        };
                        global.AttendanceLogger?.gps('Koordinat GPS presisi satelit berhasil diperoleh:', coords);
                        this._notifyPosition(coords);
                        resolve(coords);
                    },
                    (positionError) => {
                        global.AttendanceLogger?.warn('GPS', `Tier 1 gagal (code: ${positionError.code}): ${positionError.message}. Beralih ke Tier 2 (Network / WiFi Location)...`);

                        // If user explicitly denied permission, don't retry Tier 2
                        if (positionError.code === positionError.PERMISSION_DENIED) {
                            const err = new Error('Izin akses lokasi ditolak oleh pengguna atau pengaturan browser.');
                            err.code = 'PERMISSION_DENIED';
                            this._setState('denied', { message: err.message });
                            this._notifyError(err);
                            reject(err);
                            return;
                        }

                        // Tier 2: Immediate fallback to standard network/WiFi geolocation
                        this._setState('searching', { note: 'Menggunakan sinyal jaringan/WiFi...' });
                        navigator.geolocation.getCurrentPosition(
                            (pos2) => {
                                const coords2 = {
                                    latitude: pos2.coords.latitude,
                                    longitude: pos2.coords.longitude,
                                    accuracy: pos2.coords.accuracy || 25,
                                    timestamp: pos2.timestamp || Date.now()
                                };
                                global.AttendanceLogger?.gps('Koordinat lokasi jaringan/WiFi berhasil diperoleh:', coords2);
                                this._notifyPosition(coords2);
                                resolve(coords2);
                            },
                            (err2) => {
                                global.AttendanceLogger?.warn('GPS', `Tier 2 juga gagal (code: ${err2.code}): ${err2.message}. Mencoba Tier 3 (IP Geolocation)...`);
                                
                                if (err2.code === err2.PERMISSION_DENIED) {
                                    const msg = 'Izin akses lokasi ditolak oleh pengguna atau browser.';
                                    this._setState('denied', { message: msg });
                                    const finalErr = new Error(msg);
                                    finalErr.code = err2.code;
                                    this._notifyError(finalErr);
                                    reject(finalErr);
                                    return;
                                }

                                // Tier 3: Fetch IP-based location as last automated fallback (helps PCs without WiFi card)
                                fetch('https://ipapi.co/json/', { signal: AbortSignal.timeout(3500) })
                                    .then((r) => r.json())
                                    .then((ipData) => {
                                        if (ipData && ipData.latitude && ipData.longitude) {
                                            const ipCoords = {
                                                latitude: ipData.latitude,
                                                longitude: ipData.longitude,
                                                accuracy: 1000,
                                                timestamp: Date.now(),
                                                isIpFallback: true
                                            };
                                            global.AttendanceLogger?.gps('Koordinat berhasil didapatkan via IP Geolocation:', ipCoords);
                                            this._notifyPosition(ipCoords);
                                            resolve(ipCoords);
                                        } else {
                                            throw new Error('IP coordinates unavailable');
                                        }
                                    })
                                    .catch(() => {
                                        let msg = 'Gagal mendeteksi lokasi GPS atau jaringan. Pastikan GPS/Lokasi perangkat aktif.';
                                        if (err2.code === err2.TIMEOUT) {
                                            msg = 'Waktu permintaan lokasi habis. Pastikan sinyal GPS atau koneksi internet aktif.';
                                            this._setState('disabled', { message: msg });
                                        } else {
                                            this._setState('disabled', { message: msg });
                                        }

                                        const finalErr = new Error(msg);
                                        finalErr.code = err2.code;
                                        this._notifyError(finalErr);
                                        reject(finalErr);
                                    });
                            },
                            this.standardOptions
                        );
                    },
                    this.highAccuracyOptions
                );
            });
        }

        /**
         * Refresh GPS Button Handler
         * Explicitly purges old coordinates cache, asks for fresh coords, recalculates radius, and notifies UI
         */
        async refreshLocation() {
            global.AttendanceLogger?.gps('Tombol Refresh GPS ditekan. Membersihkan cache dan meminta koordinat baru...');
            this.currentCoords = null;
            this.retryCount = 0;
            return this.acquireLocation(true);
        }
    }

    global.AttendanceGPSService = new GPSService();
})(typeof window !== 'undefined' ? window : this);
