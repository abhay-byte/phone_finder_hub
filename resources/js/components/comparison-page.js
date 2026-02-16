export default (initialPhones) => ({
    phones: initialPhones, // Initial payload from controller
    isSearchOpen: false,
    searchQuery: '',
    searchResults: [],
    isLoading: false,
    currentSlot: null,
    isScrolled: false,
    showUeps: false,
    showGpx: false,

    specs: [
        {
            title: 'Top Metrics',
            rows: [] // Handled in header now
        },
        {
            title: 'UEPS Breakdown',
            rows: [
                { key: 'ueps_details.breakdown.Display Tech', label: 'Display & Design' },
                { key: 'ueps_details.breakdown.Processing & Memory', label: 'Performance' },
                { key: 'ueps_details.breakdown.Camera Mastery', label: 'Camera' },
                { key: 'ueps_details.breakdown.Power & Charging', label: 'Battery' },
                { key: 'ueps_details.breakdown.Connectivity', label: 'Connectivity' },
                { key: 'ueps_details.breakdown.Audio & Extras', label: 'Audio & Extras' },
                { key: 'ueps_details.breakdown.Developer Freedom', label: 'Dev Freedom' }
            ]
        },
        {
            title: 'Gaming (GPX-300)',
            rows: [
                { key: 'gpx_score', label: 'Overall Score' },
                { key: 'gpx_details.soc_gpu', label: 'SoC & GPU' },
                { key: 'gpx_details.sustained', label: 'Stability' },
                { key: 'gpx_details.display', label: 'Gaming Display' },
                { key: 'gpx_details.emulator', label: 'Emulation' }
            ]
        },
        {
            title: 'Raw Benchmarks',
            rows: [
                { key: 'benchmarks.antutu_score', label: 'AnTuTu v11' },
                { key: 'benchmarks.geekbench_single', label: 'Geekbench 6 (Single)' },
                { key: 'benchmarks.geekbench_multi', label: 'Geekbench 6 (Multi)' },
                { key: 'benchmarks.dmark_wild_life_extreme', label: '3DMark Wild Life Extreme' }
            ]
        },
        {
            title: 'Network',
            rows: [
                { key: 'connectivity.network_bands', label: 'Technology' },
                { key: 'connectivity.sar_value', label: 'SAR Value' },
            ]
        },
        {
            title: 'Body',
            rows: [
                { key: 'body.dimensions', label: 'Dimensions' },
                { key: 'body.weight', label: 'Weight' },
                { key: 'body.build_material', label: 'Build' },
                { key: 'body.sim', label: 'SIM' },
                { key: 'body.ip_rating', label: 'IP Rating' }
            ]
        },
        {
            title: 'Display',
            rows: [
                { key: 'body.display_type', label: 'Type' },
                { key: 'body.display_size', label: 'Size' },
                { key: 'body.display_resolution', label: 'Resolution' },
                { key: 'body.pixel_density', label: 'Density' },
                { key: 'body.screen_to_body_ratio', label: 'Screen-to-Body' },
                { key: 'body.display_brightness', label: 'Brightness' },
                { key: 'body.pwm_dimming', label: 'PWM Dimming' },
                { key: 'body.touch_sampling_rate', label: 'Touch Sampling' },
                { key: 'body.display_protection', label: 'Protection' },
                { key: 'body.screen_glass', label: 'Glass' },
            ]
        },
        {
            title: 'Platform',
            rows: [
                { key: 'platform.os', label: 'OS' },
                { key: 'platform.chipset', label: 'Chipset' },
                { key: 'platform.cpu', label: 'CPU' },
                { key: 'platform.gpu', label: 'GPU' }
            ]
        },
        {
            title: 'Memory',
            rows: [
                { key: 'platform.memory_card_slot', label: 'Card Slot' },
                { key: 'platform.internal_storage', label: 'Internal' },
                { key: 'platform.ram', label: 'RAM' },
                { key: 'platform.storage_type', label: 'Storage Type' }
            ]
        },
        {
            title: 'Main Camera',
            rows: [
                { key: 'camera.main_camera_specs', label: 'Modules' },
                { key: 'camera.main_camera_sensors', label: 'Sensors' },
                { key: 'camera.main_camera_apertures', label: 'Apertures' },
                { key: 'camera.main_camera_focal_lengths', label: 'Focal Lengths' },
                { key: 'camera.main_camera_ois', label: 'OIS' },
                { key: 'camera.main_camera_features', label: 'Features' },
                { key: 'camera.main_video_capabilities', label: 'Video' }
            ]
        },
        {
            title: 'Selfie Camera',
            rows: [
                { key: 'camera.selfie_camera_specs', label: 'Modules' },
                { key: 'camera.selfie_camera_features', label: 'Features' },
                { key: 'camera.selfie_video_capabilities', label: 'Video' }
            ]
        },
        {
            title: 'Sound',
            rows: [
                { key: 'connectivity.loudspeaker', label: 'Loudspeaker' },
                { key: 'connectivity.audio_quality', label: 'Audio Quality' },
                { key: 'connectivity.has_3_5mm_jack', label: '3.5mm Jack' }
            ]
        },
        {
            title: 'Comms',
            rows: [
                { key: 'connectivity.wlan', label: 'WLAN' },
                { key: 'connectivity.bluetooth', label: 'Bluetooth' },
                { key: 'connectivity.positioning', label: 'Positioning' },
                { key: 'connectivity.nfc', label: 'NFC' },
                { key: 'connectivity.infrared', label: 'Infrared' },
                { key: 'connectivity.radio', label: 'Radio' },
                { key: 'connectivity.usb', label: 'USB' }
            ]
        },
        {
            title: 'Features',
            rows: [
                { key: 'connectivity.sensors', label: 'Sensors' }
            ]
        },
        {
            title: 'Battery',
            rows: [
                { key: 'battery.battery_type', label: 'Type' },
                { key: 'battery.charging_specs_detailed', label: 'Charging' },
                { key: 'battery.charging_reverse', label: 'Reverse' },
            ]
        },
        {
            title: 'Misc',
            rows: [
                { key: 'body.colors', label: 'Colors' },
                { key: 'benchmarks.repairability_score', label: 'Repairability' },
                { key: 'benchmarks.energy_label', label: 'Energy Label' },
                { key: 'price', label: 'Price' }
            ]
        }
    ],

    init() {
        // Sync initial state
        this.syncState();
        window.addEventListener('keydown', (e) => {
            if (e.key === '/' && !this.isSearchOpen) {
                e.preventDefault();
                this.openSearch(this.phones.length);
            }
        });
    },

    getMax(key) {
        if (this.phones.length === 0) return 0;
        const getValue = (phone) => {
            const val = key.split('.').reduce((obj, k) => obj && obj[k], phone);
            return parseFloat(val) || 0;
        };
        return Math.max(...this.phones.map(getValue));
    },

    getBarWidth(phone, key) {
        const max = this.getMax(key);
        if (max === 0) return 0;
        const val = key.split('.').reduce((obj, k) => obj && obj[k], phone);
        const score = parseFloat(val) || 0;
        return (score / max) * 100;
    },

    getPercentageDiff(phone, key) {
        const max = this.getMax(key);
        if (max === 0) return 0;
        const val = key.split('.').reduce((obj, k) => obj && obj[k], phone);
        const score = parseFloat(val) || 0;
        if (score === max) return 0;
        const diff = (1 - (score / max)) * 100;
        return Math.round(diff);
    },

    isWinner(phone, key) {
        if (this.phones.length < 2) return false;
        const max = this.getMax(key);
        if (max === 0) return false;
        const val = key.split('.').reduce((obj, k) => obj && obj[k], phone);
        const score = parseFloat(val) || 0;
        return score === max;
    },

    formatScore(val) {
        if (!val) return '-';
        return new Intl.NumberFormat('en-IN').format(val);
    },

    getSpecValue(phone, key) {
        if (key === 'price') {
            return this.formatPrice(phone.price);
        }
        let val = key.split('.').reduce((obj, k) => obj && obj[k], phone);
        if (!val) return '<span class="text-gray-400">-</span>';
        return val.toString().replace(/\n/g, '<br>');
    },

    getRawSpecValue(phone, key) {
        return key.split('.').reduce((obj, k) => obj && obj[k], phone);
    },

    getPositiveDetails(phone, key) {
        const raw = this.getRawSpecValue(phone, key);
        if (!raw || !raw.details) return [];
        return raw.details.filter(d => d.points > 0);
    },

    formatPrice(price) {
        if (!price) return 'N/A';
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0
        }).format(price);
    },

    openSearch(index) {
        this.isSearchOpen = true;
        this.currentSlot = index;
        this.searchQuery = '';
        this.searchResults = [];
        this.$nextTick(() => document.querySelector('input[x-model="searchQuery"]').focus());
    },

    async performSearch() {
        if (this.searchQuery.length < 2) {
            this.searchResults = [];
            return;
        }
        this.isLoading = true;
        try {
            const response = await fetch(`/phones/search?query=${encodeURIComponent(this.searchQuery)}`);
            let data = await response.json();
            const currentIds = this.phones.map(p => p.id);
            this.searchResults = data.filter(p => !currentIds.includes(p.id));
        } catch (e) {
            console.error('Search failed', e);
        } finally {
            this.isLoading = false;
        }
    },

    selectPhone(result) {
        if (!result || !result.id) return;
        window.location.assign(`/compare?ids=${this.getNewIds(result.id)}`);
    },

    removePhone(id) {
        const newIds = this.phones.filter(p => p.id !== id).map(p => p.id).join(',');
        window.location.assign(`/compare?ids=${newIds}`);
    },

    clearAll() {
        window.location.assign(`/compare`);
    },

    getNewIds(newId) {
        const currentIds = this.phones.map(p => p.id);
        return [...currentIds, newId].join(',');
    },

    syncState() {
        // Local storage sync if needed in future
    }
});
