/**
 * ScheduleService - Server-Time Synchronizer & Automatic Session Resolver
 * Strictly determines attendance windows (Masuk, Siang, Pulang) based on server time, NOT browser clock.
 */
(function (global) {
    'use strict';

    class ScheduleService {
        constructor() {
            this.serverTimeOffsetMs = 0; // Difference between server time and local device time
            this.serverDate = '';
            this.currentTimeStr = '00:00:00';
            this.timezoneLabel = 'WITA';
            this.activeSession = null;
            this.scheduleConfig = {
                morning_open: '06:00',
                morning_close: '11:59',
                morning_late: '07:30',
                dzuhur_open: '12:00',
                dzuhur_close: '13:30',
                afternoon_open: '14:00',
                afternoon_close: '18:00'
            };
            this.statusEndpoint = '/admin/teacher-attendances/check-status';
            this.clockTimer = null;
            this.syncTimer = null;
            this.listeners = {
                tick: [],
                sessionChange: []
            };
        }

        on(event, fn) {
            if (this.listeners[event] && typeof fn === 'function') {
                this.listeners[event].push(fn);
                if (event === 'tick') {
                    try {
                        fn(this.getServerTimeString(), this.timezoneLabel);
                    } catch (e) {}
                }
            }
        }

        /**
         * Initialize with optional preloaded server data from Blade
         * @param {Object} initialData 
         */
        init(initialData = {}) {
            if (initialData.schedule) {
                this.scheduleConfig = Object.assign({}, this.scheduleConfig, initialData.schedule);
            }
            if (initialData.timezoneLabel) {
                this.timezoneLabel = initialData.timezoneLabel;
            }
            if (initialData.serverTime) {
                this.setServerTime(initialData.serverTime, initialData.serverDate);
            }
            if (initialData.activeSession) {
                this.activeSession = initialData.activeSession;
            }

            this.startClock();
            // Sync with backend every 60 seconds to maintain schedule accuracy
            this.syncWithBackend();
            this.syncTimer = setInterval(() => this.syncWithBackend(), 60000);
        }

        /**
         * Set server time and compute clock offset
         */
        setServerTime(timeStr, dateStr = '') {
            try {
                const now = new Date();
                const [h, m, s] = timeStr.split(':').map(Number);
                const serverMoment = new Date(now.getFullYear(), now.getMonth(), now.getDate(), h, m, s || 0);
                this.serverTimeOffsetMs = serverMoment.getTime() - now.getTime();
                this.serverDate = dateStr || now.toISOString().split('T')[0];
                global.AttendanceLogger?.schedule(`Server time synchronized: ${timeStr} (Offset: ${this.serverTimeOffsetMs}ms)`);
            } catch (e) {
                console.error('[ScheduleService] Error setting server time:', e);
            }
        }

        /**
         * Get current synced server time as Date object
         */
        getServerDate() {
            return new Date(Date.now() + this.serverTimeOffsetMs);
        }

        /**
         * Get current synced server time as HH:mm:ss
         */
        getServerTimeString() {
            const d = this.getServerDate();
            const pad = (n) => String(n).padStart(2, '0');
            return `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
        }

        startClock() {
            if (this.clockTimer) clearInterval(this.clockTimer);

            const tick = () => {
                this.currentTimeStr = this.getServerTimeString();
                this.listeners.tick.forEach((fn) => {
                    try { fn(this.currentTimeStr, this.timezoneLabel); } catch (e) {}
                });
            };

            tick();
            this.clockTimer = setInterval(tick, 1000);
        }

        /**
         * Fetch live status and schedule from backend endpoint
         */
        async syncWithBackend() {
            try {
                const res = await fetch(this.statusEndpoint, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!res.ok) return;
                const data = await res.json();

                if (data.server_time) {
                    this.setServerTime(data.server_time, data.server_date);
                }
                if (data.active_session) {
                    const prevType = this.activeSession?.type;
                    this.activeSession = data.active_session;
                    if (prevType !== data.active_session.type) {
                        this.listeners.sessionChange.forEach((fn) => {
                            try { fn(this.activeSession); } catch (e) {}
                        });
                    }
                }
                if (data.schedule) {
                    this.scheduleConfig = Object.assign({}, this.scheduleConfig, data.schedule);
                }

                global.AttendanceLogger?.schedule('Schedule synced from server backend:', data.active_session);
                return data;
            } catch (err) {
                global.AttendanceLogger?.warn('SCHEDULE', 'Failed to sync schedule from backend:', err);
            }
        }

        /**
         * Resolve active session directly from server time
         * Auto-determines window:
         * 07.00–11.59: Absen Masuk (check_in)
         * 12.00–13.30: Absen Siang (midday)
         * 14.00–18.00: Absen Pulang (check_out)
         */
        resolveCurrentSession() {
            if (this.activeSession && this.activeSession.type) {
                return this.activeSession;
            }

            const now = this.getServerDate();
            const hours = now.getHours();
            const mins = now.getMinutes();
            const currentHm = `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}`;

            const s = this.scheduleConfig;

            if (currentHm >= s.morning_open && currentHm <= s.morning_close) {
                return {
                    type: 'check_in',
                    name: 'Sesi Pagi (Masuk)',
                    short_name: 'Absen Masuk',
                    action_label: 'Presensi Masuk',
                    time_range: `${s.morning_open} - ${s.morning_close} ${this.timezoneLabel}`,
                    is_active: true
                };
            }

            if (currentHm >= s.dzuhur_open && currentHm <= s.dzuhur_close) {
                return {
                    type: 'midday',
                    name: 'Sesi Siang (Dzuhur)',
                    short_name: 'Absen Siang',
                    action_label: 'Konfirmasi Presensi Siang',
                    time_range: `${s.dzuhur_open} - ${s.dzuhur_close} ${this.timezoneLabel}`,
                    is_active: true
                };
            }

            if (currentHm >= s.afternoon_open && currentHm <= s.afternoon_close) {
                return {
                    type: 'check_out',
                    name: 'Sesi Sore (Pulang)',
                    short_name: 'Absen Pulang',
                    action_label: 'Presensi Pulang',
                    time_range: `${s.afternoon_open} - ${s.afternoon_close} ${this.timezoneLabel}`,
                    is_active: true
                };
            }

            return {
                type: 'outside_window',
                name: 'Di Luar Jadwal',
                short_name: 'Tutup',
                action_label: 'Di Luar Jam Presensi',
                time_range: 'Presensi Ditutup',
                is_active: false
            };
        }

        /**
         * Get Human-Friendly Label for Dinas Luar Mode
         * Automatically reflects active window according to server time
         */
        getDinasLuarSessionInfo() {
            const session = this.resolveCurrentSession();
            let label = 'Absen Masuk (Dinas Luar)';

            if (session.type === 'midday') {
                label = 'Absen Siang (Dinas Luar)';
            } else if (session.type === 'check_out') {
                label = 'Absen Pulang (Dinas Luar)';
            } else if (session.type === 'outside_window') {
                label = 'Dinas Luar (Di Luar Jadwal)';
            }

            return {
                sessionType: session.type,
                sessionName: session.name,
                dinasLabel: label,
                isActive: session.is_active
            };
        }
    }

    global.AttendanceScheduleService = new ScheduleService();
})(typeof window !== 'undefined' ? window : this);
