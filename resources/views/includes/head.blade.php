<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta name="google-site-verification" content="m99jAWOtAHKuav-ogqP4vWP2iGI2lkMFddfMfRnDuUo" />
<meta name="yandex-verification" content="2825b98b1bc9e5be" />

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)}; m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)}) (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym"); ym(82716877, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true });
</script>
<!-- /Yandex.Metrika counter -->

<meta property="og:type" content="website">
<meta property="og:description" content="RedGroup - Real Estate Company">
<meta property="og:site_name" content="RedGroup">
<meta property="og:url" content="{{url('/')}}">
@if (Request::route() && Request::route()->getName() == 'projects.single')
  <meta property="og:title" content="{{$project->title}}">
  @foreach($files as $img)
    @if($img->block_id == 'top_gallery_img')
        @php $og_image = $img->url; break; @endphp
    @endif
  @endforeach
  @if (isset($og_image))
    <meta property="og:image" content="{{$og_image}}">
    <meta property="og:image:secure" content="{{$og_image}}">
    <meta property="og:image:url" content="{{$og_image}}">
    <meta property="og:image:secure_url" content="{{$og_image}}">
  @endif
@else
  <meta property="og:title" content="RedGroup - Real Estate Company">
  <meta property="og:image" content="{{asset('/storage/logo.png')}}">
  <meta property="og:image:secure" content="{{asset('/storage/logo.png')}}">
  <meta property="og:image:url" content="{{asset('/storage/logo.png')}}">
  <meta property="og:image:secure_url" content="{{asset('/storage/logo.png')}}">
@endif

@if(isset($project))
    <!-- SEO meta -->
    @foreach($project->seo_settings as $setting)
        <meta name="{{ $setting->seo_key }}" content="{{ $setting->seo_value }}">
    @endforeach
@endif

<title>{{ isset($project->seo_settings[0]) ? $project->seo_settings[0]->seo_value : 'Redgroup.am' }}</title>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/script.js') }}"></script>
<script src="{{ asset('js/carousel.js') }}"></script>
<script src="{{ asset('js/owl.carousel.min.js') }}"></script>

<link rel="icon" href="/favicon.svg">

<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

<!-- Styles -->
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<link href="{{ asset('css/projects.css') }}" rel="stylesheet">
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<link href="{{ asset('css/owl.carousel.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/style-header.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">

@if(Auth::check())
  <script src="{{asset('js/ckeditor/ckeditor.js')}}"></script>
  <script src="{{asset('js/ckeditor/config.js')}}"></script>
  <script src="{{asset('js/ckeditor/styles.js')}}"></script>
@endif

{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script> --}}

<link href="{{ asset('css/jquery.multiselect.css') }}" rel="stylesheet" />
<script defer src="{{ asset('js/bootstrap-multiselect.min.js') }}" type="text/javascript"></script>

@if(Auth::check())
  <script src="{{asset('js/ckeditor/ckeditor.js')}}"></script>
  <script src="{{asset('js/ckeditor/config.js')}}"></script>
  <script src="{{asset('js/ckeditor/styles.js')}}"></script>
@endif

@if(env('APP_ENV') == 'production')
  <!-- Facebook Pixel Code -->
  <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '918537509245536');
    fbq('track', 'PageView');
  </script>
  <noscript>
    <img height="1" width="1" style="display:none" 
        src="https://www.facebook.com/tr?id=918537509245536&ev=PageView&noscript=1"/>
  </noscript>
  <!-- End Facebook Pixel Code -->
@endif
