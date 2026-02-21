<?php

namespace App\Services\SEO;

class SEOData
{
    public function __construct(
        public string $title = 'Find the Best Phones | PhoneFinderHub',
        public string $description = 'Compare specifications, features, and prices of the latest smartphones.',
        public ?string $image = null,
        public string $url = '',
        public string $type = 'website',
        public array $keywords = [],
        public string $robots = 'index, follow',
        public ?array $schema = null,
    ) {
        $this->image ??= asset('assets/logo.png');
        $this->url = $url ?: url()->current();
    }
}
