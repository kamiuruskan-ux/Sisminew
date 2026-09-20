/**
 * FingerprintService - Official DigitalPersona Web SDK Integration
 * Interfaces with HID DigitalPersona Local Device Access running on https://localhost:52181.
 * Optimized for HID DigitalPersona U.are.U 4500 USB Optical Scanner.
 * 
 * Terminal Realtime States:
 * 🟢 Scanner terhubung
 * 🟡 Menunggu sidik jari
 * 🔵 Sedang membaca
 * ✅ Fingerprint berhasil dibaca
 * 🔴 Scanner tidak ditemukan
 */
(function (global) {
    'use strict';

    class FingerprintService {
        constructor() {
            this.reader = null;
            this.isSdkLoaded = false;
            this.serviceActive = false;
            this.deviceConnected = false;
            this.activeDeviceUid = null;
            this.deviceName = 'HID DigitalPersona U.are.U 4500';
            this.sslEndpoint = 'https://127.0.0.1:52181/get_connection';
            
            // Terminal Status State
            // 'service_unavailable' | 'ssl_unauthorized' | 'device_disconnected' | 'device_connected' | 'waiting_finger' | 'reading' | 'sample_acquired' | 'error'
            this.status = 'device_disconnected';
            this.statusText = '🔴 Scanner tidak ditemukan';
            this.statusBadge = '🔴 Scanner tidak ditemukan';
            this.isAcquiring = false;
            this.isStartingCapture = false;
            this.isProcessingSample = false;
            // Native format for U.are.U 4500 is Intermediate (2 - Minutiae features)
            this.workingFormat = 2;
            this.lastSampleImage = null;

            this.listeners = {
                statusChange: [],
                sampleCaptured: [],
                error: []
            };

            // Auto-reconnect / re-arm when user returns to or focuses this window
            if (typeof window !== 'undefined') {
                let focusDebounceTimer = null;
                const handleWakeup = () => {
                    if (focusDebounceTimer) clearTimeout(focusDebounceTimer);
                    focusDebounceTimer = setTimeout(async () => {
                        global.AttendanceLogger?.fingerprint('Window/Tab resumed focus.');
                        if (!this.deviceConnected && (this.status === 'ssl_unauthorized' || this.status === 'service_unavailable')) {
                            const alive = await this.checkServiceAlive();
                            if (alive) {
                                await this.init();
                            }
                        } else if (this.deviceConnected && !this.isAcquiring && !this.isStartingCapture) {
                            global.AttendanceLogger?.fingerprint('Window focused: re-arming fingerprint sensor acquisition...');
                            await this.startCapture();
                        }
                    }, 300);
                };

                window.addEventListener('focus', handleWakeup);
                if (typeof document !== 'undefined') {
                    document.addEventListener('visibilitychange', () => {
                        if (document.visibilityState === 'visible') {
                            handleWakeup();
                        }
                    });
                }
            }
        }

        on(event, fn) {
            if (this.listeners[event] && typeof fn === 'function') {
                this.listeners[event].push(fn);
            }
        }

        _setStatus(statusKey, text, extra = null) {
            this.status = statusKey;
            this.statusText = text;
            this.statusBadge = text;

            global.AttendanceLogger?.fingerprint(`Status changed: [${statusKey}] -> ${text}`, extra);

            this.listeners.statusChange.forEach((fn) => {
                try { fn({ status: this.status, text: this.statusText, badge: this.statusBadge, isAcquiring: this.isAcquiring, format: this.workingFormat, extra }); } catch (e) { console.error(e); }
            });
        }

        /**
         * Check if DigitalPersona Local Device Access service is running on https://127.0.0.1:52181
         */
        async checkServiceAlive() {
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 2500);

                // Check standard endpoint get_connection
                const res = await fetch(this.sslEndpoint, {
                    method: 'GET',
                    mode: 'cors',
                    signal: controller.signal
                });

                clearTimeout(timeoutId);
                if (res.ok || res.status === 200 || res.status === 204) {
                    this.serviceActive = true;
                    global.AttendanceLogger?.fingerprint('DigitalPersona Local Service Active on https://127.0.0.1:52181');
                    return true;
                }
            } catch (err) {
                global.AttendanceLogger?.warn('FINGERPRINT', 'Local Device Access service not responding at ' + this.sslEndpoint + ':', err.message);
            }

            this.serviceActive = false;
            return false;
        }

        /**
         * Open SSL certificate authorization page in a new window/tab
         */
        openSslAuthorization() {
            if (typeof window !== 'undefined') {
                window.open(this.sslEndpoint, '_blank');
            }
        }

        /**
         * Initialize the official DigitalPersona Web SDK reader
         */
        async init() {
            // 1. Verify that WebSdk and dp.devices are present in global scope
            if (!global.dp || !global.dp.devices || !global.dp.devices.FingerprintReader) {
                global.AttendanceLogger?.warn('FINGERPRINT', 'DigitalPersona Web SDK scripts (@digitalpersona/devices) not yet loaded in window.');
                this._setStatus('service_unavailable', '🔴 SDK DigitalPersona belum dimuat');
                return false;
            }

            this.isSdkLoaded = true;

            try {
                // 2. Pre-check local service
                const isAlive = await this.checkServiceAlive();
                if (!isAlive) {
                    this.deviceConnected = false;
                    this._setStatus('ssl_unauthorized', '⚠️ Izin browser pada port 127.0.0.1 belum aktif', {
                        endpoint: this.sslEndpoint,
                        needSslBypass: true
                    });
                    return false;
                }

                // 3. Singleton FingerprintReader instance (Reuse existing to prevent multiple WebSocket connections)
                if (!this.reader) {
                    this.reader = new global.dp.devices.FingerprintReader();
                    this._attachSdkEventListeners();
                }

                // 4. Enumerate connected devices using SDK method
                const devices = await this.reader.enumerateDevices();
                global.AttendanceLogger?.fingerprint('EnumerateDevices result:', devices);

                if (devices && devices.length > 0) {
                    this.activeDeviceUid = devices[0];
                    this.deviceConnected = true;
                    this._setStatus('device_connected', '🟢 Scanner terhubung', { deviceUid: this.activeDeviceUid });
                    
                    // Directly arm the sensor using native Intermediate format
                    return await this.startCapture();
                } else {
                    this.deviceConnected = false;
                    this.activeDeviceUid = null;
                    this._setStatus('device_disconnected', '🔴 Scanner tidak ditemukan');
                    return false;
                }
            } catch (err) {
                global.AttendanceLogger?.error('FINGERPRINT', 'Failed to initialize DigitalPersona FingerprintReader:', err);
                const msg = err.message || '';
                const isCertOrWsBlocked = msg.includes('Communication failed') ||
                    msg.includes('Cannot load configuration') ||
                    msg.includes('Failed to fetch') ||
                    msg.includes('NetworkError');

                if (isCertOrWsBlocked) {
                    this.deviceConnected = false;
                    this._setStatus('ssl_unauthorized', '⚠️ Izin WebSocket Chrome (#allow-insecure-localhost) Diperlukan', {
                        endpoint: this.sslEndpoint,
                        needChromeFlag: true,
                        rawError: msg
                    });
                } else {
                    this._setStatus('error', '🔴 Gagal menghubungkan scanner: ' + (msg || 'Service offline'));
                }
                return false;
            }
        }

        /**
         * Attach native SDK event handlers without polling
         */
        _attachSdkEventListeners() {
            if (!this.reader) return;

            // Event: Scanner Connected (Plugged in)
            this.reader.on('DeviceConnected', async (event) => {
                global.AttendanceLogger?.fingerprint('Event: DeviceConnected', event);
                this.deviceConnected = true;
                this.activeDeviceUid = event.deviceId || event.deviceUid || this.activeDeviceUid;
                this._setStatus('device_connected', '🟢 Scanner terhubung', { deviceUid: this.activeDeviceUid });
                
                // Automatically start acquisition when device is plugged in
                await this.startCapture();
            });

            // Event: Scanner Disconnected (Unplugged)
            this.reader.on('DeviceDisconnected', (event) => {
                global.AttendanceLogger?.fingerprint('Event: DeviceDisconnected', event);
                this.deviceConnected = false;
                this.isAcquiring = false;
                this.isProcessingSample = false;
                this.activeDeviceUid = null;
                this._setStatus('device_disconnected', '🔴 Scanner tidak ditemukan', { deviceUid: event.deviceId || event.deviceUid });
            });

            // Event: Acquisition Started (Hardware optical glass pad illuminated & ready)
            this.reader.on('AcquisitionStarted', (event) => {
                global.AttendanceLogger?.fingerprint('Event: AcquisitionStarted - Ready for finger placement', event);
                this.isAcquiring = true;
                this.isProcessingSample = false;
                this._setStatus('waiting_finger', '🟡 Menunggu sidik jari');
            });

            // Event: Acquisition Stopped
            this.reader.on('AcquisitionStopped', (event) => {
                global.AttendanceLogger?.fingerprint('Event: AcquisitionStopped', event);
                this.isAcquiring = false;

                // CRITICAL FOR U.are.U 4500:
                // When finger touches sensor, QualityReported fires -> optical acquisition ends -> AcquisitionStopped fires -> SamplesAcquired is delivered!
                // DO NOT wipe the UI state back to 'device_connected' or idle while reading or processing a sample!
                if (this.isProcessingSample || this.status === 'reading' || this.status === 'sample_acquired') {
                    global.AttendanceLogger?.fingerprint('AcquisitionStopped ignored: finger scan / sample reading in progress.');
                    return;
                }

                // Sync UI status when acquisition ceases normally (and not during verification)
                if (this.deviceConnected && this.status !== 'error') {
                    this._setStatus('device_connected', '🟢 Scanner terhubung');
                }
            });

            // Event: Quality Reported / Finger Touching Sensor
            this.reader.on('QualityReported', (event) => {
                global.AttendanceLogger?.fingerprint('Event: QualityReported (Sensor reading)', event);
                this.isProcessingSample = true;
                this._setStatus('reading', '🔵 Sedang membaca...', { quality: event.quality });
            });

            // Event: Samples Acquired (Fingerprint successfully read)
            this.reader.on('SamplesAcquired', async (event) => {
                global.AttendanceLogger?.fingerprint('Event: SamplesAcquired - Fingerprint scan success!', event);
                this.isAcquiring = false;
                this.isProcessingSample = true;
                this._setStatus('sample_acquired', '✅ Fingerprint berhasil dibaca');

                let sampleData = null;
                if (event.samples && event.samples.length > 0) {
                    const firstSample = event.samples[0];
                    if (typeof firstSample === 'string') {
                        sampleData = firstSample;
                    } else if (firstSample && typeof firstSample === 'object') {
                        sampleData = firstSample.Data || firstSample.sample || firstSample.data || JSON.stringify(firstSample);
                    }
                }

                if (sampleData) {
                    this.lastSampleImage = sampleData;
                }

                // Notify listeners with sample data
                this.listeners.sampleCaptured.forEach((fn) => {
                    try { fn(sampleData, event); } catch (e) { console.error(e); }
                });

                // Clear isProcessingSample after a safe duration
                setTimeout(() => {
                    this.isProcessingSample = false;
                }, 3000);
            });

            // Event: Hardware Error Occurred
            this.reader.on('ErrorOccurred', (event) => {
                global.AttendanceLogger?.error('FINGERPRINT', 'Event: ErrorOccurred', event);
                this.isAcquiring = false;
                this.isProcessingSample = false;
                this._setStatus('error', '🔴 Terjadi kendala scanner (Kode: ' + (event.error || 'Err') + ')');
                this.listeners.error.forEach((fn) => {
                    try { fn(event); } catch (e) {}
                });

                // Attempt auto-recovery
                setTimeout(async () => {
                    if (this.deviceConnected && !this.isAcquiring && !this.isStartingCapture) {
                        await this.startCapture(true);
                    }
                }, 2500);
            });

            // Event: Communication Failed (Local agent not reachable)
            this.reader.on('CommunicationFailed', (event) => {
                global.AttendanceLogger?.error('FINGERPRINT', 'Event: CommunicationFailed - Local agent unreachable', event);
                this.serviceActive = false;
                this.deviceConnected = false;
                this.isAcquiring = false;
                this.isProcessingSample = false;
                this._setStatus('service_unavailable', '🔴 Service DigitalPersona tidak berjalan');
            });
        }

        /**
         * Start biometric fingerprint acquisition
         * Optimized for U.are.U 4500: uses SampleFormat.Intermediate (2)
         * DigitalPersona Web SDK expects wildcard reader ID ("00000000-0000-0000-0000-000000000000").
         * Never pass device GUID with braces as it causes driver rejection (E_INVALIDARG).
         * @param {boolean} force - Force stop and re-arm even if previously marked as acquiring
         */
        async startCapture(force = false) {
            if (!this.reader || !this.deviceConnected) {
                global.AttendanceLogger?.warn('FINGERPRINT', 'Cannot start capture: reader not ready or device disconnected.');
                return false;
            }

            // If already actively acquiring and not forced, keep running without disrupting driver
            if (this.isAcquiring && !force) {
                global.AttendanceLogger?.fingerprint('Sensor is already actively acquiring.');
                return true;
            }

            // Concurrency guard: avoid duplicate parallel startCapture executions
            if (this.isStartingCapture) {
                global.AttendanceLogger?.fingerprint('startCapture is already in progress, skipping duplicate call.');
                return false;
            }

            this.isStartingCapture = true;

            try {
                // If forced or currently acquiring, safely stop first and let COM driver settle
                if (this.isAcquiring || force) {
                    try {
                        await this.reader.stopAcquisition();
                        // 200ms buffer to give Windows USB HID driver time to reset state cleanly
                        await new Promise((res) => setTimeout(res, 200));
                    } catch (e) {
                        // Ignore stop errors
                    }
                    this.isAcquiring = false;
                }

                // Native format for HID DigitalPersona U.are.U 4500:
                // Primary: SampleFormat.Intermediate (2) - standard minutiae extraction
                // Fallback: SampleFormat.Raw (1)
                const SF = global.dp?.devices?.SampleFormat || {};
                const primaryFormat = SF.Intermediate ?? 2;
                const fallbackFormat = SF.Raw ?? 1;

                const candidates = [primaryFormat];
                if (fallbackFormat !== primaryFormat) {
                    candidates.push(fallbackFormat);
                }

                let lastError = null;

                for (const format of candidates) {
                    try {
                        global.AttendanceLogger?.fingerprint(`Calling startAcquisition(format=${format})...`);
                        // IMPORTANT: Never pass device GUID as second argument!
                        // Omitting it defaults to "00000000-0000-0000-0000-000000000000" in Web SDK,
                        // which cleanly targets the connected physical scanner without GUID syntax errors.
                        await this.reader.startAcquisition(format);

                        // Acquisition successfully started on physical hardware!
                        this.workingFormat = format;
                        this.isAcquiring = true;
                        this.isProcessingSample = false;
                        this._setStatus('waiting_finger', '🟡 Menunggu sidik jari', { format });
                        global.AttendanceLogger?.fingerprint(`🟢 Sensor aktif! Format=${format} siap menerima tempelan jari.`);
                        return true;
                    } catch (err) {
                        lastError = err;
                        global.AttendanceLogger?.warn('FINGERPRINT', `startAcquisition failed with format=${format}:`, err?.message || err);
                        await new Promise((res) => setTimeout(res, 250));
                    }
                }

                // If all candidates failed
                this.isAcquiring = false;
                const errMsg = lastError?.message || 'Driver menolak perintah startAcquisition';
                this._setStatus('error', `🔴 Gagal mengaktifkan sensor (${errMsg})`, { error: lastError });
                return false;
            } finally {
                this.isStartingCapture = false;
            }
        }

        /**
         * Stop biometric fingerprint acquisition
         */
        async stopCapture() {
            if (!this.reader) return;
            try {
                await this.reader.stopAcquisition();
                this.isAcquiring = false;
                this.isProcessingSample = false;
                if (this.deviceConnected) {
                    this._setStatus('device_connected', '🟢 Scanner terhubung');
                }
            } catch (err) {
                global.AttendanceLogger?.warn('FINGERPRINT', 'stopAcquisition error:', err);
            }
        }

        /**
         * Trigger manual reconnect/refresh of DigitalPersona scanner
         */
        async refreshScanner() {
            global.AttendanceLogger?.fingerprint('Manual scanner re-detect requested...');
            return this.init();
        }
    }

    global.AttendanceFingerprintService = new FingerprintService();
})(typeof window !== 'undefined' ? window : this);
