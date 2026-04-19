// Driver Tracking Module
const DriverTracker = {
    pickupId: null,
    trackingInterval: null,
    map: null,
    markerDriver: null,
    markerPickup: null,

    init: function(pickupId) {
        this.pickupId = pickupId;
        this.startTracking();
        this.setupEventListeners();
    },

    startTracking: function() {
        const self = this;
        
        // Initial fetch
        this.fetchDriverData();
        
        // Update every 5 seconds
        this.trackingInterval = setInterval(function() {
            self.fetchDriverData();
        }, 5000);
    },

    fetchDriverData: function() {
        const self = this;
        
        $.ajax({
            url: '/api/tracking.php',
            type: 'GET',
            data: { 
                action: 'get_driver_status', 
                pickup_id: this.pickupId 
            },
            dataType: 'json',
            success: function(data) {
                self.updateDisplay(data);
                self.updateMap(data);
            },
            error: function(error) {
                console.log('Tracking unavailable: Driver not yet assigned');
            }
        });
    },

    updateDisplay: function(data) {
        if (!data.driver_name) return;

        // Update driver info
        $('#driver-name').text(data.driver_name);
        $('#driver-vehicle').text(data.vehicle_number || 'N/A');
        $('#driver-status').html(`
            <span class="badge bg-${data.status === 'busy' ? 'warning' : 'success'}">
                <i class="fas fa-${data.status === 'busy' ? 'spinner fa-spin' : 'check-circle'}"></i>
                ${data.status === 'busy' ? 'On the way' : 'Available'}
            </span>
        `);

        // Update last updated time
        if (data.last_updated) {
            const lastUpdate = new Date(data.last_updated);
            const now = new Date();
            const diffSeconds = Math.floor((now - lastUpdate) / 1000);
            
            let timeStr = '';
            if (diffSeconds < 60) {
                timeStr = 'just now';
            } else if (diffSeconds < 3600) {
                const mins = Math.floor(diffSeconds / 60);
                timeStr = `${mins} minute${mins > 1 ? 's' : ''} ago`;
            } else {
                const hours = Math.floor(diffSeconds / 3600);
                timeStr = `${hours} hour${hours > 1 ? 's' : ''} ago`;
            }
            $('#driver-last-update').text(`Last updated: ${timeStr}`);
        }

        // Show/hide tracking info based on driver assignment
        if (data.driver_id && data.latitude && data.longitude) {
            $('#driver-tracking-info').show();
            $('#driver-coordinates').html(`
                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                <code>${data.latitude.toFixed(6)}, ${data.longitude.toFixed(6)}</code>
            `);
        } else if (data.driver_id) {
            $('#driver-tracking-info').html(`
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>Driver location not available yet
                </div>
            `).show();
        }
    },

    updateMap: function(data) {
        // Initialize map if not already done
        if (!this.map) {
            this.initializeMap();
        }

        if (!data.latitude || !data.longitude) return;

        const driverLocation = { lat: parseFloat(data.latitude), lng: parseFloat(data.longitude) };
        
        // Update or create driver marker
        if (this.markerDriver) {
            this.markerDriver.setPosition(driverLocation);
        } else {
            this.markerDriver = new google.maps.Marker({
                position: driverLocation,
                map: this.map,
                title: `Driver: ${data.driver_name}`,
                icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
            });
        }

        // Center map on driver
        this.map.setCenter(driverLocation);
    },

    initializeMap: function() {
        // Only initialize if Google Maps API is loaded
        if (typeof google === 'undefined') return;

        this.map = new google.maps.Map(document.getElementById('tracking-map'), {
            zoom: 15,
            center: { lat: 0, lng: 0 },
            streetViewControl: false,
            fullscreenControl: true
        });
    },

    setupEventListeners: function() {
        // Refresh button
        $('#refresh-tracking-btn').on('click', function() {
            DriverTracker.fetchDriverData();
        });
    },

    stopTracking: function() {
        if (this.trackingInterval) {
            clearInterval(this.trackingInterval);
        }
    }
};

// Driver Location Update (for drivers)
const DriverLocationUpdater = {
    init: function() {
        if (navigator.geolocation) {
            this.startLocationTracking();
        }
    },

    startLocationTracking: function() {
        const self = this;
        
        // Get location immediately
        this.sendLocation();
        
        // Update every 10 seconds
        setInterval(function() {
            self.sendLocation();
        }, 10000);
    },

    sendLocation: function() {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                $.ajax({
                    url: '/api/tracking.php',
                    type: 'POST',
                    data: {
                        action: 'update_driver_location',
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    },
                    dataType: 'json'
                });
            },
            function(error) {
                console.log('Geolocation error:', error.message);
            }
        );
    }
};
