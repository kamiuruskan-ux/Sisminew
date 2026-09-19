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

            // High-precision GPS options
            this.gpsOptions = {
                enableHighAccuracy: true,
                maximumAge: 0, // Never use cached coordinates
                timeout: 15000 // 15 seconds timeout
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
            const permStatus = await permService.queryGeolocationPermission();

            global.AttendanceLogger?.permission(`GPSService.init - Permission status: ${permStatus}`);

            // Listen for permission change in browser (e.g. user toggles Allow in address bar)
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

            if (permStatus === 'granted') {
                // Immediately acquire coordinates
                return this.acquireLocation(false);
            } else if (permStatus === 'prompt') {
                this._setState('prompt', { message: 'Izin akses lokasi diperlukan.' });
                // Attempt to prompt browser permission modal
                return this.acquireLocation(false);
            } else if (permStatus === 'denied') {
                this._setState('denied', { message: 'Izin akses lokasi ditolak oleh browser.' });
                return Promise.reject(new Error('Geolocation permission denied'));
            } else {
                this._setState('disabled', { message: 'Browser tidak mendukung geolokasi GPS.' });
                return Promise.reject(new Error('Geolocation unsupported'));
            }
        }

        /**
         * Core method to acquire location from device GPS
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
                // Force purge cached coordinates
                this.currentCoords = null;
                this.retryCount = 0;
            }

            this._setState(this.retryCount > 0 ? 'retrying' : 'searching', {
                retry: this.retryCount,
                maxRetries: this.maxRetries
            });

            global.AttendanceLogger?.gps(`Mencari koordinat satelit GPS (Percobaan ${this.retryCount + 1}/${this.maxRetries + 1})...`);

            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const coords = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy,
                            timestamp: position.timestamp || Date.now()
                        };

                        global.AttendanceLogger?.gps('Koordinat GPS berhasil diperoleh:', coords);
                        this._notifyPosition(coords);
                        resolve(coords);
                    },
                    (positionError) => {
                        global.AttendanceLogger?.warn('GPS', `Pencarian koordinat gagal (code: ${positionError.code}): ${positionError.message}`);

                        // Code 1: PERMISSION_DENIED
                        if (positionError.code === positionError.PERMISSION_DENIED) {
                            const err = new Error('Izin akses lokasi ditolak oleh pengguna atau pengaturan browser.');
                            err.code = 'PERMISSION_DENIED';
                            this._setState('denied', { message: err.message });
                            this._notifyError(err);
                            reject(err);
                            return;
                        }

                        // Code 2: POSITION_UNAVAILABLE or Code 3: TIMEOUT
                        if (this.retryCount < this.maxRetries) {
                            this.retryCount++;
                            global.AttendanceLogger?.gps(`Menjalankan retry otomatis ${this.retryCount}/${this.maxRetries} dalam 1 detik...`);
                            this._setState('retrying', { retry: this.retryCount, maxRetries: this.maxRetries });

                            setTimeout(() => {
                                this.acquireLocation(false).then(resolve).catch(reject);
                            }, 1000);
                        } else {
                            let msg = 'Sinyal satelit GPS tidak dapat dijangkau atau waktu habis.';
                            if (positionError.code === positionError.POSITION_UNAVAILABLE) {
                                msg = 'Layanan lokasi/GPS pada perangkat dalam keadaan nonaktif atau berada di luar jangkauan satelit.';
                            } else if (positionError.code === positionError.TIMEOUT) {
                                msg = 'Waktu permintaan lokasi satelit habis (timeout 15 detik).';
                            }

                            const err = new Error(msg);
                            err.code = positionError.code;
                            this._setState('disabled', { message: msg });
                            this._notifyError(err);
                            reject(err);
                        }
                    },
                    this.gpsOptions
                );
            });
        }

        /**
         * Refresh GPS Button Handler
         * Explicitly purges old coordinates cache, asks for fresh coords, recalculates radius, and notifies UI
         */
        async refreshLocation() {
            global.AttendanceLogger?.gps('Tombol Refresh GPS ditekan. Membersihkan cache dan meminta koordinat satelit baru...');
            this.currentCoords = null;
            this.retryCount = 0;
            return this.acquireLocation(true);
        }
    }

    global.AttendanceGPSService = new GPSService();
})(typeof window !== 'undefined' ? window : this);
