/**
 * FingerprintService - Official DigitalPersona Web SDK Integration
 * Interfaces with HID DigitalPersona Local Device Access running on https://localhost:52181.
 * Uses native SDK events (@digitalpersona/devices & @digitalpersona/core) without excessive polling.
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

            this.listeners = {
                statusChange: [],
                sampleCaptured: [],
                error: []
            };

            // Auto-reconnect when user returns to this tab (after authorizing SSL in another tab)
            if (typeof window !== 'undefined') {
                window.addEventListener('focus', async () => {
                    if (!this.deviceConnected && (this.status === 'ssl_unauthorized' || this.status === 'service_unavailable')) {
                        global.AttendanceLogger?.fingerprint('Window focused: re-checking DigitalPersona connection...');
                        const alive = await this.checkServiceAlive();
                        if (alive) {
                            await this.init();
                        }
                    }
                });
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
                try { fn({ status: this.status, text: this.statusText, badge: this.statusBadge, extra }); } catch (e) { console.error(e); }
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
                // Network error / cert untrusted / PNA blocked
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

                // 3. Instantiate official SDK FingerprintReader
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
                    this._setStatus('device_connected', '🟢 Scanner terhubung');
                    await this.startCapture();
                    return true;
                } else {
                    this.deviceConnected = false;
                    this._setStatus('device_disconnected', '🔴 Scanner tidak ditemukan');
                    return false;
                }
            } catch (err) {
                global.AttendanceLogger?.error('FINGERPRINT', 'Failed to initialize DigitalPersona FingerprintReader:', err);
                // If it fails with communication error, trigger SSL bypass prompt
                if (err.message && (err.message.includes('Cannot load configuration') || err.message.includes('Failed to fetch') || err.message.includes('NetworkError'))) {
                    this.deviceConnected = false;
                    this._setStatus('ssl_unauthorized', '⚠️ Izin browser pada port 127.0.0.1 belum aktif', {
                        endpoint: this.sslEndpoint,
                        needSslBypass: true,
                        rawError: err.message
                    });
                } else {
                    this._setStatus('error', '🔴 Gagal menghubungkan scanner: ' + (err.message || 'Service offline'));
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
                this.activeDeviceUid = event.deviceUid;
                this._setStatus('device_connected', '🟢 Scanner terhubung', { deviceUid: event.deviceUid });
                
                // Automatically start acquisition when device is plugged in
                await this.startCapture();
            });

            // Event: Scanner Disconnected (Unplugged)
            this.reader.on('DeviceDisconnected', (event) => {
                global.AttendanceLogger?.fingerprint('Event: DeviceDisconnected', event);
                this.deviceConnected = false;
                this.activeDeviceUid = null;
                this._setStatus('device_disconnected', '🔴 Scanner tidak ditemukan', { deviceUid: event.deviceUid });
            });

            // Event: Acquisition Started (Ready for touch)
            this.reader.on('AcquisitionStarted', (event) => {
                global.AttendanceLogger?.fingerprint('Event: AcquisitionStarted - Waiting for finger placement', event);
                this._setStatus('waiting_finger', '🟡 Menunggu sidik jari');
            });

            // Event: Acquisition Stopped
            this.reader.on('AcquisitionStopped', (event) => {
                global.AttendanceLogger?.fingerprint('Event: AcquisitionStopped', event);
            });

            // Event: Quality Reported / Finger Touching Sensor
            this.reader.on('QualityReported', (event) => {
                global.AttendanceLogger?.fingerprint('Event: QualityReported (Sensor reading)', event);
                this._setStatus('reading', '🔵 Sedang membaca', { quality: event.quality });
            });

            // Event: Samples Acquired (Fingerprint successfully read)
            this.reader.on('SamplesAcquired', (event) => {
                global.AttendanceLogger?.fingerprint('Event: SamplesAcquired - Fingerprint scan success!', event);
                this._setStatus('sample_acquired', '✅ Fingerprint berhasil dibaca');

                let sampleData = null;
                if (event.samples && event.samples.length > 0) {
                    const firstSample = event.samples[0];
                    sampleData = typeof firstSample === 'string' ? firstSample : (firstSample.Data || JSON.stringify(firstSample));
                }

                // Notify listeners with sample data
                this.listeners.sampleCaptured.forEach((fn) => {
                    try { fn(sampleData, event); } catch (e) { console.error(e); }
                });

                // Return to waiting state after 2 seconds
                setTimeout(() => {
                    if (this.deviceConnected) {
                        this._setStatus('waiting_finger', '🟡 Menunggu sidik jari');
                    }
                }, 2000);
            });

            // Event: Hardware Error Occurred
            this.reader.on('ErrorOccurred', (event) => {
                global.AttendanceLogger?.error('FINGERPRINT', 'Event: ErrorOccurred', event);
                this._setStatus('error', '🔴 Terjadi kendala scanner (Kode: ' + (event.error || 'Err') + ')');
                this.listeners.error.forEach((fn) => {
                    try { fn(event); } catch (e) {}
                });
            });

            // Event: Communication Failed (Local agent not reachable)
            this.reader.on('CommunicationFailed', (event) => {
                global.AttendanceLogger?.error('FINGERPRINT', 'Event: CommunicationFailed - Local agent unreachable', event);
                this.serviceActive = false;
                this._setStatus('service_unavailable', '🔴 Service DigitalPersona tidak berjalan');
            });
        }

        /**
         * Start biometric fingerprint acquisition
         */
        async startCapture() {
            if (!this.reader || !this.deviceConnected) {
                global.AttendanceLogger?.warn('FINGERPRINT', 'Cannot start capture: reader not ready or device disconnected.');
                return;
            }

            try {
                // SampleFormat: Intermediate (2) or Raw (1) or PngImage (5)
                const sampleFormat = global.dp.devices.SampleFormat 
                    ? global.dp.devices.SampleFormat.Intermediate 
                    : 2;

                await this.reader.startAcquisition(sampleFormat, this.activeDeviceUid);
                this._setStatus('waiting_finger', '🟡 Menunggu sidik jari');
            } catch (err) {
                global.AttendanceLogger?.warn('FINGERPRINT', 'startAcquisition error:', err);
            }
        }

        /**
         * Stop biometric fingerprint acquisition
         */
        async stopCapture() {
            if (!this.reader) return;
            try {
                await this.reader.stopAcquisition(this.activeDeviceUid);
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
