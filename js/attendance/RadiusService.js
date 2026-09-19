/**
 * RadiusService - Geofencing & Distance Calculation Service
 * Uses precise Haversine formula to compute distance between coordinates.
 */
(function (global) {
    'use strict';

    class RadiusService {
        /**
         * Calculate distance between two points in meters using Haversine formula
         * @param {number} lat1 
         * @param {number} lon1 
         * @param {number} lat2 
         * @param {number} lon2 
         * @returns {number} Distance in meters (rounded)
         */
        calculateDistance(lat1, lon1, lat2, lon2) {
            if (lat1 === null || lon1 === null || lat2 === null || lon2 === null) {
                return null;
            }

            const R = 6371e3; // Earth radius in meters
            const toRad = (deg) => (deg * Math.PI) / 180;

            const φ1 = toRad(lat1);
            const φ2 = toRad(lat2);
            const Δφ = toRad(lat2 - lat1);
            const Δλ = toRad(lon2 - lon1);

            const a =
                Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            const distance = Math.round(R * c);

            global.AttendanceLogger?.radius(`Calculated distance: ${distance}m (P1: ${lat1},${lon1} -> P2: ${lat2},${lon2})`);
            return distance;
        }

        /**
         * Verify if distance is within allowed school radius
         * @param {number} distance In meters
         * @param {number} maxRadius Allowed radius in meters
         * @returns {boolean}
         */
        isWithinRadius(distance, maxRadius) {
            if (distance === null || typeof distance === 'undefined') return false;
            return distance <= maxRadius;
        }

        /**
         * Format distance in human readable string
         * @param {number} meters 
         * @returns {string} e.g. "45m" or "1.3 km"
         */
        formatDistance(meters) {
            if (meters === null || typeof meters === 'undefined') return '-';
            if (meters < 1000) {
                return `${meters}m`;
            }
            return `${(meters / 1000).toFixed(1)} km`;
        }

        /**
         * Generate a human-friendly explanation if outside radius
         * @param {number} distance 
         * @param {number} maxRadius 
         * @param {string} schoolName 
         * @returns {string}
         */
        getOutsideRadiusExplanation(distance, maxRadius, schoolName = 'Sekolah') {
            const excess = distance - maxRadius;
            return `Posisi Anda berada ${this.formatDistance(distance)} dari gerbang ${schoolName} (melebihi batas maksimal ${maxRadius}m sebanyak ${this.formatDistance(excess)}). Harap berada di area sekolah untuk melakukan presensi reguler WFO.`;
        }
    }

    global.AttendanceRadiusService = new RadiusService();
})(typeof window !== 'undefined' ? window : this);
