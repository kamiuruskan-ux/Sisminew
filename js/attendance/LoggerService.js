/**
 * LoggerService - Telemetry & Logging Module for Attendance System
 * Provides colorized, structured logging with easy production toggle.
 */
(function (global) {
    'use strict';

    class LoggerService {
        constructor() {
            // Default to true in development, easily disabled in production
            this.enabled = typeof global.ATTENDANCE_DEBUG !== 'undefined' ? Boolean(global.ATTENDANCE_DEBUG) : true;
            this.history = [];
            this.maxHistory = 100;
        }

        setEnabled(enabled) {
            this.enabled = Boolean(enabled);
            global.ATTENDANCE_DEBUG = this.enabled;
        }

        _log(level, category, message, data) {
            const timestamp = new Date().toISOString().substring(11, 19);
            const entry = { timestamp, level, category, message, data };
            
            this.history.push(entry);
            if (this.history.length > this.maxHistory) {
                this.history.shift();
            }

            if (!this.enabled && level !== 'error') {
                return;
            }

            const colors = {
                GPS: '#10B981',
                FINGERPRINT: '#3B82F6',
                SCHEDULE: '#8B5CF6',
                RADIUS: '#F59E0B',
                ATTENDANCE: '#EC4899',
                PERMISSION: '#06B6D4',
                BACKEND: '#14B8A6'
            };

            const tagColor = colors[category.toUpperCase()] || '#64748B';
            const badgeStyle = `background: ${tagColor}; color: #fff; font-weight: bold; padding: 2px 6px; border-radius: 4px; font-size: 10px;`;
            const timeStyle = 'color: #94A3B8; font-size: 10px;';

            const consoleMethod = level === 'error' ? console.error : (level === 'warn' ? console.warn : console.log);

            if (data !== undefined) {
                consoleMethod(`%c${timestamp}%c %c[${category}]%c ${message}`, timeStyle, '', badgeStyle, '', data);
            } else {
                consoleMethod(`%c${timestamp}%c %c[${category}]%c ${message}`, timeStyle, '', badgeStyle, '');
            }
        }

        gps(message, data) {
            this._log('info', 'GPS', message, data);
        }

        fingerprint(message, data) {
            this._log('info', 'FINGERPRINT', message, data);
        }

        permission(message, data) {
            this._log('info', 'PERMISSION', message, data);
        }

        schedule(message, data) {
            this._log('info', 'SCHEDULE', message, data);
        }

        radius(message, data) {
            this._log('info', 'RADIUS', message, data);
        }

        attendance(message, data) {
            this._log('info', 'ATTENDANCE', message, data);
        }

        warn(category, message, data) {
            this._log('warn', category, message, data);
        }

        error(category, message, data) {
            this._log('error', category, message, data);
        }

        getHistory() {
            return this.history;
        }
    }

    global.AttendanceLogger = new LoggerService();
})(typeof window !== 'undefined' ? window : this);
