{{-- 
    Performance Optimization: Resource Hints and Preloading
    Include this in your layout head section
--}}

{{-- DNS Prefetch for external resources --}}
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//cdn.jsdelivr.net">
<link rel="dns-prefetch" href="//unpkg.com">

{{-- Preconnect to important origins --}}
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

{{-- Preload critical assets --}}
@if(config('app.env') === 'production')
    {{-- Preload critical CSS --}}
    @vite(['resources/css/app.css'], 'preload')
    
    {{-- Preload critical JS --}}
    @vite(['resources/js/app.js'], 'preload')
@endif

{{-- Performance hints --}}
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

{{-- Resource hints for better performance --}}
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'">

{{-- Defer non-critical CSS --}}
<noscript>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</noscript>

