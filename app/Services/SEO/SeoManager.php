<?php

namespace App\Services\SEO;

class SeoManager
{
    private SEOData $data;

    public function __construct()
    {
        $this->data = new SEOData(); // Loads defaults
    }

    public function set(SEOData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function get(): SEOData
    {
        return $this->data;
    }
}
