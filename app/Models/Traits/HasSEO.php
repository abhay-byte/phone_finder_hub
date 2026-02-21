<?php

namespace App\Models\Traits;

use App\Services\SEO\SEOData;

trait HasSEO
{
    abstract public function getSEOData(): SEOData;
}
