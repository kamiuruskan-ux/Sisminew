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
            this.isAcquiring = false;
            this.workingFormat = null;
            this.lastSampleImage = null;

            this.listeners = {
                statusChange: [],
                sampleCaptured: [],
                error: []
            };

            // Auto-reconnect / re-arm when user returns to or focuses this window
            if (typeof window !== 'undefined') {
                window.addEventListener('focus', async () => {
                    global.AttendanceLogger?.fingerprint('Window focused.');
                    if (!this.deviceConnected && (this.status === 'ssl_unauthorized' || this.status === 'service_unavailable')) {
                        const alive = await this.checkServiceAlive();
                        if (alive) {
                            await this.init();
                        }
                    } else if (this.deviceConnected && !this.isAcquiring) {
                        global.AttendanceLogger?.fingerprint('Window focused: re-arming fingerprint sensor acquisition...');
                        await this.startCapture();
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

                // 3. Instantiate fresh official SDK FingerprintReader
                if (this.reader) {
                    try { 
                        await this.reader.stopAcquisition(); 
                        this.reader.off(); 
                    } catch (e) {}
                }
                this.reader = new global.dp.devices.FingerprintReader();
                this._attachSdkEventListeners();

                // 4. Enumerate connected devices using SDK method
                const devices = await this.reader.enumerateDevices();
                global.AttendanceLogger?.fingerprint('EnumerateDevices result:', devices);

                if (devices && devices.length > 0) {
                    this.activeDeviceUid = devices[0];
                    this.deviceConnected = true;
                    this._setStatus('device_connected', '🟢 Scanner terhubung');
                    
                    // Directly arm the sensor
                    const captureStarted = await this.startCapture();
                    return captureStarted;
                } else {
                    this.deviceConnected = false;
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
                this.activeDeviceUid = null;
                this._setStatus('device_disconnected', '🔴 Scanner tidak ditemukan', { deviceUid: event.deviceId || event.deviceUid });
            });

            // Event: Acquisition Started (Ready for touch)
            this.reader.on('AcquisitionStarted', (event) => {
                global.AttendanceLogger?.fingerprint('Event: AcquisitionStarted - Ready for finger placement', event);
                this.isAcquiring = true;
                this._setStatus('waiting_finger', '🟡 Menunggu sidik jari');
            });

            // Event: Acquisition Stopped
            this.reader.on('AcquisitionStopped', (event) => {
                global.AttendanceLogger?.fingerprint('Event: AcquisitionStopped', event);
                this.isAcquiring = false;
            });

            // Event: Quality Reported / Finger Touching Sensor
            this.reader.on('QualityReported', (event) => {
                global.AttendanceLogger?.fingerprint('Event: QualityReported (Sensor reading)', event);
                this._setStatus('reading', '🔵 Sedang membaca...', { quality: event.quality });
            });

            // Event: Samples Acquired (Fingerprint successfully read)
            this.reader.on('SamplesAcquired', async (event) => {
                global.AttendanceLogger?.fingerprint('Event: SamplesAcquired - Fingerprint scan success!', event);
                this.isAcquiring = false;
                this._setStatus('sample_acquired', '✅ Fingerprint berhasil dibaca');

                let sampleData = null;
                if (event.samples && event.samples.length > 0) {
                    const firstSample = event.samples[0];
                    if (typeof firstSample === 'string') {
                        sampleData = firstSample;
                    } else if (firstSample && typeof firstSample === 'object') {
                        sampleData = firstSample.Data || firstSample.sample || JSON.stringify(firstSample);
                    }
                }

                // If sampleData is base64 png or raw, record last sample
                if (sampleData) {
                    this.lastSampleImage = sampleData;
                }

                // Notify listeners with sample data
                this.listeners.sampleCaptured.forEach((fn) => {
                    try { fn(sampleData, event); } catch (e) { console.error(e); }
                });

                // Auto re-arm sensor acquisition after brief 1.5s delay
                setTimeout(async () => {
                    if (this.deviceConnected) {
                        await this.startCapture();
                    }
                }, 1500);
            });

            // Event: Hardware Error Occurred
            this.reader.on('ErrorOccurred', (event) => {
                global.AttendanceLogger?.error('FINGERPRINT', 'Event: ErrorOccurred', event);
                this.isAcquiring = false;
                this._setStatus('error', '🔴 Terjadi kendala scanner (Kode: ' + (event.error || 'Err') + ')');
                this.listeners.error.forEach((fn) => {
                    try { fn(event); } catch (e) {}
                });

                // Attempt auto-recovery
                setTimeout(async () => {
                    if (this.deviceConnected) {
                        await this.startCapture();
                    }
                }, 2500);
            });

            // Event: Communication Failed (Local agent not reachable)
            this.reader.on('CommunicationFailed', (event) => {
                global.AttendanceLogger?.error('FINGERPRINT', 'Event: CommunicationFailed - Local agent unreachable', event);
                this.serviceActive = false;
                this.deviceConnected = false;
                this.isAcquiring = false;
                this._setStatus('service_unavailable', '🔴 Service DigitalPersona tidak berjalan');
            });
        }

        /**
         * Start biometric fingerprint acquisition with smart multi-format auto-fallback
         */
        async startCapture() {
            if (!this.reader || !this.deviceConnected) {
                global.AttendanceLogger?.warn('FINGERPRINT', 'Cannot start capture: reader not ready or device disconnected.');
                return false;
            }

            // Always safely stop any previous acquisition state before starting fresh
            try {
                await this.reader.stopAcquisition();
            } catch (e) {}

            // Prioritized format candidate list:
            // 1. PngImage (5) - Standard for web UI capture
            // 2. Raw (1) - Universal raw optical format
            // 3. Intermediate (2) - Minutiae features
            const formatCandidates = [];
            const SF = global.dp?.devices?.SampleFormat || {};

            if (this.workingFormat !== null) {
                formatCandidates.push(this.workingFormat);
            }
            
            if (SF.PngImage !== undefined && !formatCandidates.includes(SF.PngImage)) formatCandidates.push(SF.PngImage);
            if (!formatCandidates.includes(5)) formatCandidates.push(5);
            if (SF.Raw !== undefined && !formatCandidates.includes(SF.Raw)) formatCandidates.push(SF.Raw);
            if (!formatCandidates.includes(1)) formatCandidates.push(1);
            if (SF.Intermediate !== undefined && !formatCandidates.includes(SF.Intermediate)) formatCandidates.push(SF.Intermediate);
            if (!formatCandidates.includes(2)) formatCandidates.push(2);

            // Candidate device target list:
            // 1. undefined / default wildcard (00000000-0000-0000-0000-000000000000)
            // 2. specific activeDeviceUid
            const deviceCandidates = [undefined];
            if (this.activeDeviceUid && !deviceCandidates.includes(this.activeDeviceUid)) {
                deviceCandidates.push(this.activeDeviceUid);
            }

            let lastError = null;

            for (const format of formatCandidates) {
                for (const devId of deviceCandidates) {
                    try {
                        global.AttendanceLogger?.fingerprint(`Attempting startAcquisition with format=${format}, devId=${devId || 'default'}`);
                        await this.reader.startAcquisition(format, devId);

                        // Acquisition successfully started on physical hardware!
                        this.workingFormat = format;
                        this.isAcquiring = true;
                        this._setStatus('waiting_finger', '🟡 Menunggu sidik jari');
                        global.AttendanceLogger?.fingerprint(`🟢 Sensor aktif! Format=${format} siap menerima tempelan jari.`);
                        return true;
                    } catch (err) {
                        lastError = err;
                        global.AttendanceLogger?.warn('FINGERPRINT', `startAcquisition failed with format=${format}:`, err?.message || err);
                    }
                }
            }

            // If all candidates failed
            this.isAcquiring = false;
            const errMsg = lastError?.message || 'Driver menolak perintah startAcquisition';
            this._setStatus('error', `🔴 Gagal mengaktifkan sensor (${errMsg})`, { error: lastError });
            return false;
        }

        /**
         * Stop biometric fingerprint acquisition
         */
        async stopCapture() {
            if (!this.reader) return;
            try {
                await this.reader.stopAcquisition(this.activeDeviceUid);
                this.isAcquiring = false;
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
