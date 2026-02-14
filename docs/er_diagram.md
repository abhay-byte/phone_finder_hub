# Phone Finder - Entity Relationship Diagram

```mermaid
erDiagram
    PHONES {
        id integer PK
        name string
        brand string
        model_variant string
        price decimal
        overall_score integer
        release_date date
        image_url string
        created_at timestamp
        updated_at timestamp
    }

    SPEC_BODIES {
        id integer PK
        phone_id integer FK
        dimensions string
        weight string
        build_material string
        sim string
        ip_rating string
        colors string
        display_type string
        display_size string
        display_resolution string
        display_protection string
        display_features text
        created_at timestamp
        updated_at timestamp
    }

    SPEC_PLATFORMS {
        id integer PK
        phone_id integer FK
        os string
        chipset string
        cpu string
        gpu string
        memory_card_slot string
        internal_storage string
        ram string
        storage_type string
        created_at timestamp
        updated_at timestamp
    }

    SPEC_CAMERAS {
        id integer PK
        phone_id integer FK
        main_camera_specs text
        main_camera_features text
        main_video_capabilities string
        selfie_camera_specs text
        selfie_camera_features text
        selfie_video_capabilities string
        created_at timestamp
        updated_at timestamp
    }

    SPEC_CONNECTIVITIES {
        id integer PK
        phone_id integer FK
        wlan string
        bluetooth string
        positioning string
        nfc string
        infrared string
        radio string
        usb string
        sensors text
        loudspeaker string
        jack_3_5mm string
        created_at timestamp
        updated_at timestamp
    }

    SPEC_BATTERIES {
        id integer PK
        phone_id integer FK
        battery_type string
        charging_wired string
        charging_wireless string
        charging_reverse string
        created_at timestamp
        updated_at timestamp
    }

    BENCHMARKS {
        id integer PK
        phone_id integer FK
        antutu_score integer
        geekbench_single integer
        geekbench_multi integer
        dmark_wild_life_extreme integer
        battery_endurance_hours decimal
        battery_active_use_score string
        created_at timestamp
        updated_at timestamp
    }

    PHONES ||--|| SPEC_BODIES : "has body specs"
    PHONES ||--|| SPEC_PLATFORMS : "has platform specs"
    PHONES ||--|| SPEC_CAMERAS : "has camera specs"
    PHONES ||--|| SPEC_CONNECTIVITIES : "has connectivity specs"
    PHONES ||--|| SPEC_BATTERIES : "has battery specs"
    PHONES ||--|| BENCHMARKS : "has scores"
```
