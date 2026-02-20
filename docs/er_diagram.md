# Phone Finder - Entity Relationship Diagram

This diagram represents the complete database schema for the Phone Finder Hub application, including all tables, columns, relationships, indexes, and constraints.

## Core Application Tables

```mermaid
erDiagram
    PHONES {
        id integer PK
        name string UK "Unique constraint"
        brand string "Indexed"
        model_variant string
        price decimal "Indexed"
        overall_score decimal "Changed to decimal(5,1), Indexed"
        ueps_score decimal "Indexed"
        value_score decimal "Indexed"
        expert_score decimal "Nullable"
        gpx_score decimal "Indexed"
        gpx_details json
        cms_score decimal "Indexed, nullable, default 0"
        cms_details json "Nullable"
        endurance_score decimal "Default 0"
        release_date date "Indexed"
        announced_date date
        image_url string
        amazon_url string
        flipkart_url string
        amazon_price decimal
        flipkart_price decimal
        created_at timestamp
        updated_at timestamp
    }

    SPEC_BODIES {
        id integer PK
        phone_id integer FK
        dimensions string
        weight string
        build_material string
        cooling_type string
        sim string
        ip_rating string
        colors string
        display_type string
        display_size string
        display_resolution string
        display_protection string
        display_brightness string
        measured_display_brightness string
        pwm_dimming string
        screen_to_body_ratio string
        pixel_density string
        touch_sampling_rate string
        screen_glass string
        display_features text
        screen_area string
        aspect_ratio string
        glass_protection_level string
        created_at timestamp
        updated_at timestamp
    }

    SPEC_PLATFORMS {
        id integer PK
        phone_id integer FK
        os string
        os_details string
        os_openness string
        chipset string
        cpu string
        gpu string
        gpu_emulation_tier string
        memory_card_slot string
        internal_storage string
        storage_min integer
        storage_max integer
        ram string
        ram_min integer
        ram_max integer
        storage_type string
        bootloader_unlockable boolean
        turnip_support boolean
        turnip_support_level string
        aosp_aesthetics_score integer
        custom_rom_support string
        created_at timestamp
        updated_at timestamp
    }

    SPEC_CAMERAS {
        id integer PK
        phone_id integer FK
        main_camera_specs text
        main_camera_sensors text
        main_camera_apertures text
        main_camera_focal_lengths text
        main_camera_features text
        main_camera_ois string
        main_camera_zoom string
        main_camera_pdaf string
        main_video_capabilities string
        ultrawide_camera_specs string
        telephoto_camera_specs string
        selfie_camera_specs text
        selfie_camera_sensor string
        selfie_camera_aperture string
        selfie_camera_features text
        selfie_camera_autofocus boolean
        selfie_video_capabilities string
        selfie_video_features string
        video_features text
        created_at timestamp
        updated_at timestamp
    }

    SPEC_CONNECTIVITIES {
        id integer PK
        phone_id integer FK
        wlan string
        wifi_bands string
        bluetooth string
        positioning string
        positioning_details text
        network_bands string
        nfc string
        infrared string
        radio string
        usb string
        usb_details string
        sensors text
        loudspeaker string
        audio_quality string
        loudness_test_result string
        sar_value string
        jack_3_5mm string
        has_3_5mm_jack boolean
        created_at timestamp
        updated_at timestamp
    }

    SPEC_BATTERIES {
        id integer PK
        phone_id integer FK
        battery_type string
        charging_wired string
        charging_specs_detailed string
        charging_wireless string
        charging_reverse string
        reverse_wired string
        reverse_wireless string
        created_at timestamp
        updated_at timestamp
    }

    BENCHMARKS {
        id integer PK
        phone_id integer FK
        antutu_score integer "Indexed"
        antutu_v10_score integer
        geekbench_single integer "Indexed"
        geekbench_multi integer "Indexed"
        dmark_wild_life_extreme integer "Indexed"
        dmark_wild_life_stress_stability integer
        dmark_test_type string
        battery_endurance_hours decimal "Indexed"
        battery_active_use_score string
        charge_time_test string
        repairability_score string
        free_fall_rating string
        energy_label string
        dxomark_score integer "Nullable, for CMS calculation"
        phonearena_camera_score integer "Nullable, for CMS calculation"
        other_benchmark_score integer "Nullable"
        created_at timestamp
        updated_at timestamp
    }

    PHONES ||--|| SPEC_BODIES : "has body specs"
    PHONES ||--|| SPEC_PLATFORMS : "has platform specs"
    PHONES ||--|| SPEC_CAMERAS : "has camera specs"
    PHONES ||--|| SPEC_CONNECTIVITIES : "has connectivity specs"
    PHONES ||--|| SPEC_BATTERIES : "has battery specs"
    PHONES ||--|| BENCHMARKS : "has benchmark scores"
```

## Authentication & Session Tables

```mermaid
erDiagram
    USERS {
        id integer PK
        name string
        username string UK "Unique, used for login"
        email string UK "Unique constraint"
        email_verified_at timestamp
        password string "bcrypt hashed"
        role string "user | super_admin | author | maintainer | moderator"
        remember_token string
        created_at timestamp
        updated_at timestamp
    }

    PASSWORD_RESET_TOKENS {
        email string PK
        token string
        created_at timestamp
    }

    SESSIONS {
        id string PK
        user_id integer FK "Indexed, nullable"
        ip_address string
        user_agent text
        payload longtext
        last_activity integer "Indexed"
    }

    PERSONAL_ACCESS_TOKENS {
        id integer PK
        tokenable_type string
        tokenable_id integer
        name text
        token string UK "Unique constraint"
        abilities text
        last_used_at timestamp
        expires_at timestamp "Indexed"
        created_at timestamp
        updated_at timestamp
    }

    USERS ||--o{ SESSIONS : "has sessions"
    USERS ||--o{ COMMENTS : "writes comments"
    USERS ||--o{ COMMENT_UPVOTES : "upvotes comments"

    COMMENTS {
        id integer PK
        phone_id integer FK "Indexed"
        user_id integer FK "Indexed, Nullable"
        parent_id integer FK "Nullable, self-referential for replies"
        content text
        upvotes_count integer "Default 0"
        created_at timestamp
        updated_at timestamp
    }

    COMMENT_UPVOTES {
        id integer PK
        comment_id integer FK "Indexed"
        user_id integer FK "Indexed"
        created_at timestamp
        updated_at timestamp
    }

    COMMENTS ||--o{ COMMENTS : "has replies (parent_id)"
    COMMENTS ||--o{ COMMENT_UPVOTES : "receives upvotes"
```

## Community & Content Tables (Blogs & Forums)

```mermaid
erDiagram
    BLOGS {
        id integer PK
        title string
        slug string UK "Unique"
        content longtext
        excerpt text "Nullable"
        featured_image string "Nullable"
        user_id integer FK
        is_published boolean "Default false"
        published_at timestamp "Nullable"
        created_at timestamp
        updated_at timestamp
    }

    FORUM_CATEGORIES {
        id integer PK
        name string
        slug string UK "Unique"
        description text "Nullable"
        created_at timestamp
        updated_at timestamp
    }

    FORUM_POSTS {
        id integer PK
        forum_category_id integer FK "Cascade down"
        user_id integer FK "Cascade down"
        title string
        slug string UK "Unique"
        content longtext
        views integer "Default 0"
        created_at timestamp
        updated_at timestamp
    }

    FORUM_COMMENTS {
        id integer PK
        forum_post_id integer FK "Cascade down"
        user_id integer FK "Cascade down"
        content longtext
        created_at timestamp
        updated_at timestamp
    }

    USERS ||--o{ BLOGS : "authors"
    FORUM_CATEGORIES ||--o{ FORUM_POSTS : "contains"
    USERS ||--o{ FORUM_POSTS : "creates"
    FORUM_POSTS ||--o{ FORUM_COMMENTS : "has comments"
    USERS ||--o{ FORUM_COMMENTS : "writes"
```

## System Tables (Cache & Jobs)

```mermaid
erDiagram
    CACHE {
        key string PK
        value mediumtext
        expiration integer "Indexed"
    }

    CACHE_LOCKS {
        key string PK
        owner string
        expiration integer "Indexed"
    }

    JOBS {
        id integer PK
        queue string "Indexed"
        payload longtext
        attempts tinyint
        reserved_at integer
        available_at integer
        created_at integer
    }

    JOB_BATCHES {
        id string PK
        name string
        total_jobs integer
        pending_jobs integer
        failed_jobs integer
        failed_job_ids longtext
        options mediumtext
        cancelled_at integer
        created_at integer
        finished_at integer
    }

    FAILED_JOBS {
        id integer PK
        uuid string UK "Unique constraint"
        connection text
        queue text
        payload longtext
        exception longtext
        failed_at timestamp
    }
```

## Schema Summary

### Total Tables: 17

#### Core Application Tables (7):
1. **phones** - Main phone records with pricing, scores, and metadata
2. **spec_bodies** - Physical specifications and display details
3. **spec_platforms** - OS, chipset, and developer freedom metrics
4. **spec_cameras** - Camera specifications for main, ultrawide, telephoto, and selfie
5. **spec_connectivities** - Network, connectivity, and audio specifications
6. **spec_batteries** - Battery capacity and charging specifications
7. **benchmarks** - Performance benchmarks and test results

#### Authentication & Engagement Tables (4):
8. **users** - User accounts with `username` (unique login handle), `role` (user | super_admin | author | maintainer | moderator)
9. **password_reset_tokens** - Password reset functionality
10. **sessions** - User session management
11. **personal_access_tokens** - API token authentication
12. **comments** - User comments on phones
13. **comment_upvotes** - Upvotes on comments

#### Community & Content Tables (4):
14. **blogs** - Articles written by authors/admins
15. **forum_categories** - High level categories for forum discussions
16. **forum_posts** - User-created discussion threads
17. **forum_comments** - Replies to forum posts

#### System Tables (2):
18. **cache** & **cache_locks** - Application caching
19. **jobs**, **job_batches**, **failed_jobs** - Queue management

### Key Relationships

- **One-to-One**: Each phone has exactly one record in each spec table (bodies, platforms, cameras, connectivities, batteries, benchmarks)
- **Cascade Deletion**: All spec tables use `onDelete('cascade')` - deleting a phone removes all related specs
- **Foreign Keys**: All spec tables reference `phones.id` via `phone_id`

### Indexes

**phones table:**
- `name` (unique)
- `overall_score`, `ueps_score`, `value_score`, `gpx_score`, `cms_score`
- `price`, `release_date`

**benchmarks table:**
- `antutu_score`, `geekbench_single`, `geekbench_multi`
- `dmark_wild_life_extreme`, `battery_endurance_hours`

### Notable Schema Features

1. **Scoring System**: Multiple scoring metrics (overall_score, ueps_score, value_score, expert_score, gpx_score, cms_score, endurance_score) with gpx_details and cms_details stored as JSON
2. **CMS-1330 Camera Scoring**: Dedicated cms_score (0-1330) and cms_details (JSON breakdown) for comprehensive camera evaluation
3. **Camera Benchmarks**: DxOMark, PhoneArena, and other camera scores stored in benchmarks table for CMS calculation
4. **E-commerce Integration**: Amazon and Flipkart URLs and prices
5. **Developer Metrics**: Bootloader unlock, custom ROM support, Turnip support for gaming
6. **Comprehensive Camera Specs**: Separate fields for main, ultrawide, telephoto, and selfie cameras
7. **Detailed Display Specs**: Both claimed and measured brightness values
8. **Multiple Benchmark Types**: AnTuTu, Geekbench, 3DMark with stability testing
9. **Charging Specs**: Detailed wired, wireless, and reverse charging specifications
10. **Thermal Management**: Cooling type tracking for performance analysis
11. **Storage Variants**: RAM and storage min/max fields for tracking multiple configurations
