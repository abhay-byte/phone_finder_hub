# Phone Finder Hub 📱

**Find Value, Not Hype.**

Phone Finder Hub is a data-driven smartphone comparison platform built with Laravel. It helps users identify the best value-for-money devices by analyzing objective performance benchmarks against current market prices.

## Key Features

- **Value Score Algorithm**: Calculates "Points per ₹1000" based on AnTuTu benchmarks and price.
- **Comprehensive Specs**: Detailed breakdown of Platform, Body, Camera, Connectivity, and Battery specifications.
- **Benchmark Visualization**: Bar charts for AnTuTu, Geekbench, 3DMark, and Battery Endurance.
- **Dark Mode Support**: Minimalist Material Design UI with seamless light/dark theme switching.
- **Search & Filter**: Quickly find phones by name, brand, or chipset.

## Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templates, Tailwind CSS v4, Alpine.js
- **Database**: SQLite / MySQL
- **Tooling**: Vite for asset bundling

## Installation

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/Abhay-cloud/phone_finder_hub.git
    cd phone_finder_hub
    ```

2.  **Install PHP dependencies**:
    ```bash
    composer install
    ```

3.  **Install Node dependencies**:
    ```bash
    npm install
    ```

4.  **Setup Environment**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Database Migration & Seeding**:
    ```bash
    touch database/database.sqlite
    php artisan migrate --seed
    ```

6.  **Run Development Server**:
    ```bash
    npm run dev
    php artisan serve
    ```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License

[MIT](https://choosealicense.com/licenses/mit/)
