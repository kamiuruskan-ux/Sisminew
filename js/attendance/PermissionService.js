/**
 * PermissionService - Browser Permissions & Geolocation Lifecycle Manager
 * Handles permissions.query, secure context detection, and realtime permission change listeners.
 */
(function (global) {
    'use strict';

    class PermissionService {
        constructor() {
            this.status = 'prompt'; // 'granted' | 'prompt' | 'denied' | 'unsupported'
            this.permissionStatusObj = null;
            this.listeners = [];
            this.isSecure = this.checkSecureContext();
        }

        checkSecureContext() {
            if (typeof window === 'undefined') return true;
            // Secure context is true on HTTPS or localhost/127.0.0.1
            const isLocal = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname);
            const isHttps = window.location.protocol === 'https:';
            return Boolean(window.isSecureContext || isHttps || isLocal);
        }

        onChange(callback) {
            if (typeof callback === 'function') {
                this.listeners.push(callback);
            }
        }

        notifyListeners(newStatus) {
            this.status = newStatus;
            this.listeners.forEach((fn) => {
                try {
                    fn(newStatus);
                } catch (e) {
                    console.error('[PermissionService] Error in listener:', e);
                }
            });
        }

        /**
         * Query browser geolocation permission state
         * @returns {Promise<string>} 'granted' | 'prompt' | 'denied' | 'unsupported'
         */
        async queryGeolocationPermission() {
            if (!this.isSecure) {
                global.AttendanceLogger?.permission('Insecure HTTP context detected. Browser may restrict geolocation API.');
            }

            if (typeof navigator === 'undefined' || !navigator.geolocation) {
                this.status = 'unsupported';
                global.AttendanceLogger?.permission('navigator.geolocation is not supported by this browser.');
                this.notifyListeners('unsupported');
                return 'unsupported';
            }

            if (navigator.permissions && typeof navigator.permissions.query === 'function') {
                try {
                    const perm = await navigator.permissions.query({ name: 'geolocation' });
                    this.permissionStatusObj = perm;
                    this.status = perm.state; // 'granted', 'prompt', 'denied'
                    global.AttendanceLogger?.permission(`navigator.permissions status: ${this.status}`);

                    // Remove previous onchange if any, attach fresh listener
                    perm.onchange = () => {
                        global.AttendanceLogger?.permission(`Permission changed in browser to: ${perm.state}`);
                        this.notifyListeners(perm.state);
                    };

                    this.notifyListeners(this.status);
                    return this.status;
                } catch (err) {
                    global.AttendanceLogger?.warn('PERMISSION', 'navigator.permissions.query failed or not supported for geolocation:', err);
                    // Fallback to prompt so caller can try navigator.geolocation.getCurrentPosition
                    this.status = 'prompt';
                    this.notifyListeners('prompt');
                    return 'prompt';
                }
            }

            // Fallback for browsers without navigator.permissions.query
            this.status = 'prompt';
            this.notifyListeners('prompt');
            return 'prompt';
        }
    }

    global.AttendancePermissionService = new PermissionService();
})(typeof window !== 'undefined' ? window : this);
