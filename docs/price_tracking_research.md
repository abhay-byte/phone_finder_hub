# Research: Reliable Price Tracking for Amazon & Flipkart (India)

## Executive Summary
Getting reliable, real-time pricing from Amazon and Flipkart is challenging due to strict anti-scraping measures and API access requirements. The **Official APIs** are the most reliable method but have high barriers to entry (active affiliate accounts with sales). **Third-party APIs** are easier to integrate but may cost money. **Direct Scraping** is free but fragile and risky.

---

## 1. Amazon India

### Option A: Official Product Advertising API (PA-API) **[Recommended]**
This is the only 100% reliable, legal, and stable way to get prices.
*   **Pros**: Real-time data, legal, high limits, no CAPTCHAs.
*   **Cons**: High barrier to entry.
*   **Requirements**:
    1.  **Amazon Associates Account**: You must register for an affiliate account.
    2.  **Qualifying Sales**: You must generate **3 qualifying sales** within the first 180 days (or sometimes 30 days depending on current policy) to get API access.
    3.  **Active Usage**: You must maintain sales to keep API access.
*   **Laravel Package**: `rossjcooper/laravel-amazon-products`

### Option B: Third-Party APIs
Tools that handle the scraping/API rotation for you.
*   **RapidAPI (Amazon Price Data)**: Various APIs available (e.g., "Amazon Price", "Axesso").
*   **PriceHistory API**: Paid service for historical data.
*   **Pros**: Easy to start, no sales requirements.
*   **Cons**: Costs money (usually per request), improved but strictly not official.

### Option C: Web Scraping (The "Hacker" Way)
Parsing HTML from product pages.
*   **Pros**: Free.
*   **Cons**: **Extremely Unreliable**. Amazon aggressively blocks IPs and changes HTML structure. Requires rotating proxies and headless browsers (Puppeteer/Playwright).
*   **Laravel Tools**: `Goutte`, `Panther`, or `Spatie/Browsershot`.

---

## 2. Flipkart India

### Option A: Official Affiliate API
Flipkart has an API for affiliates to search products and get prices.
*   **Status**: Access is currently **Restricted**.
*   **Cons**: Flipkart has paused new direct affiliate registrations for individuals for a long time. It is very difficult to get a direct API token now.
*   **Workaround**: Use an aggregator network like **Cuelinks** or **EarnKaro**, though they provide affiliate links, not necessarily raw price data APIs.

### Option B: Third-Party APIs
*   **RapidAPI**: Search for "Flipkart Scraper" or "Indian E-commerce API".
*   **Data Scraper Libraries**: `jsartisan/shopkart-laravel` (Accesses search APIs, mileage may vary).

---

## Recommendation for "Phone Finder"

1.  **Immediate / Low Effort**:
    *   **Manual Entry**: For a boutique site like this, manually updating prices for ~50 phones once a week is often more efficient than building a complex scraper.
    *   **Community Sourcing**: Add a "Update Price" button for users to report new prices.

2.  **Professional Route**:
    *   Apply for an **Amazon Associates** account immediately. Send links to friends/family to get the first 3 sales to unlock the API. This is the "Holy Grail" for your specific use case.

3.  **Developer Route (Unstable)**:
    *   Build a small scraper using **Puppeteer** (via `spatie/browsershot` in Laravel) that runs once a day to check prices. **Warning**: Hosting this on shared hosting will fail; requires a VPS to run the headless browser.
