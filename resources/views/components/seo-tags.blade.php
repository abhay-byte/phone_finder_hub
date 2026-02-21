@inject('seoManager', 'App\Services\SEO\SeoManager')
@php $seo = $seoManager->get(); @endphp

<title>{{ $seo->title }}</title>
<meta name="description" content="{{ $seo->description }}">
@if(!empty($seo->keywords))
<meta name="keywords" content="{{ implode(', ', $seo->keywords) }}">
@endif
<meta name="robots" content="{{ $seo->robots }}">
<link rel="canonical" href="{{ $seo->url }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $seo->type }}">
<meta property="og:url" content="{{ $seo->url }}">
<meta property="og:title" content="{{ $seo->title }}">
<meta property="og:description" content="{{ $seo->description }}">
<meta property="og:image" content="{{ $seo->image }}">
<meta property="og:locale" content="{{ app()->getLocale() }}">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $seo->url }}">
<meta name="twitter:title" content="{{ $seo->title }}">
<meta name="twitter:description" content="{{ $seo->description }}">
<meta name="twitter:image" content="{{ $seo->image }}">

<!-- JSON-LD Schema -->
@if($seo->schema)
<script type="application/ld+json">
{!! json_encode($seo->schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
