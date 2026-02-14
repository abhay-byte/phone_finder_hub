# Phone Finder: UI/UX Design Document

## Design Philosophy

**"Minimalist Utility"**

The design should be clean, functional, and adherent to **Material Design 3 (Material You)** principles. It should prioritize clarity and ease of use, avoiding clutter while remaining data-rich.

-   **Style**: Material Design 3 (Flat, rounded corners, pastel accents, adaptive layouts).
-   **Theme**: Support for both **Light** and **Dark** modes with a seamless toggle in the top navigation bar.
-   **Focus**: Readability, whitespace, and clear typographic hierarchy.

## User Personas

1.  **The Gamer**: Prioritizes GPU scores (3DMark), cooling, and sustained performance. Needs "Best Gaming Phone under ₹30k".
2.  **The Value Hunter**: Wants the absolute best specs for the lowest price. Obsessed with the "Price/Performance Ratio".
3.  **The Power User**: Needs specific features (Rootable, USB 3.0, 12GB+ RAM).

## Sitemap & Navigation

1.  **Top Navigation Bar (AppBar)**
    -   Logo (Left)
    -   Links: Home, Rankings, Compare
    -   **Theme Toggle** (Sun/Moon icon for Light/Dark mode) - *Key Feature*
    -   Search Icon (Expands to full bar)
2.  **Home / Dashboard**
    -   Minimal Hero: High-impact search bar + "Find my perfect phone" CTA.
    -   "Top Value Picks": Cards highlighting best Price/Perf ratio.
    -   Quick Filters Chips: "Under ₹20k", "Flagships", "Gaming", "Camera".
3.  **Rankings (The "Data Grid" View)**
    -   Sortable DataTable: Rank | Phone | Price | AnTuTu | Geekbench | Battery | **Value Score**
4.  **Phone Details**
    -   Clean layout with large product image.
    -   Specs & Benchmarks in collapsible cards or tabs.
    -   "Verdict" Section with pros/cons chips.
5.  **Comparison Tool**
    -   Side-by-side view (Max 3-4 phones).
    -   Highlight differences.

## Key Pages & Wireframes

### 1. Home / Dashboard
-   **Hero Section**: Centered layout. Large title ("Find Value, Not Hype"). Search input with floating label.
-   **Value Champions**: Horizontal scrolling list (Carousel) of cards.
-   **Chips**: Filter chips below the search bar for quick access.

### 2. The Rankings Table (Core Feature)
A clean, Material Data Table.
-   **Design**: Elevation 1 surface. Sticky header row.
-   **Columns**: Rank, Device Name, Price (₹), AnTuTu v10, Geekbench 6 (Single/Multi), 3DMark, Battery (mAh), Charging (W), **Value Score (pts/₹1k)**.
-   **Interactions**:
    -   Click header to sort.
    -   Ripple effect on clicks.
    -   Color-coded "badges" for scores: High scores (Primary color), Low scores (Error/Warning color).

### 3. Phone Detail Page
-   **Layout**: Split screen on desktop (Image left, Details right). Stacked on mobile.
-   **Components**: Cards for different spec sections (Performance, Display, Camera).
-   **FAB (Floating Action Button)**: "Compare" button at bottom right (mobile).

### 4. Comparison View
-   **Sticky Header**: Keeps phone names visible.
-   **Highlight Differences**: Toggle switch.

## Visual Design System (Material UI / Tailwind)

-   **Colors**:
    -   **Primary**: `Indigo-600` (Light), `Indigo-300` (Dark)
    -   **Surface**: `White` (Light), `Grey-900` (Dark)
    -   **Background**: `Grey-50` (Light), `Black` (Dark)
    -   **Error**: `Red-600` (Light), `Red-300` (Dark) for poor value/specs.
    -   **Success**: `Green-600` (Light), `Green-300` (Dark) for great value/specs.
-   **Typography**:
    -   **Font**: `Roboto` or `Inter` (Standard Material fonts).
    -   **Hierarchy**: Large, bold headings. Readable body text.
-   **Components**:
    -   **Cards**: Rounded corners (`rounded-xl`), slight shadow (`shadow-md`), hover lift.
    -   **Buttons**: Filled (Primary), Outlined (Secondary), Text (Tertiary). Ripple effects.
    -   **Toggle**: Switch component for Dark/Light mode on Navbar.

## Interactions (Alpine.js)

-   **Theme Toggle**: Persist state in `localStorage`. Toggle `dark` class on `html` tag.
-   **Live Search**: Filter table rows instantly.
-   **Compare Drawer**: "Add to Compare" floats a bottom sheet (Mobile) or drawer.
-   **Charts**: Clean, minimal bar charts for benchmarks using Chart.js or similar.
