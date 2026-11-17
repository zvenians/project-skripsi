
@if(is_file(public_path('build/manifest.json')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    {{-- CDN fallback keeps motion and Vue tools available prior to the Vite build. --}}
    <script src="https://cdn.jsdelivr.net/npm/vue@3.5.43/dist/vue.global.prod.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.1/dist/gsap.min.js" defer></script>
    <script src="{{ asset('js/unai-ui.js') }}" defer></script>
    <script defer>document.addEventListener('DOMContentLoaded', function () {
        if (window.initUnaiUI) window.initUnaiUI(window.Vue, window.gsap);
    });</script>
@endif
