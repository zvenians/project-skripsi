<div id="calendar" class="mt-4" aria-label="Kalender reservasi yang disetujui"></div>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('calendar');
    if (!el || !window.FullCalendar) return;
    new FullCalendar.Calendar(el, {
        initialView: window.innerWidth < 600 ? 'listMonth' : 'dayGridMonth',
        locale: 'id',
        height: 'auto',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listMonth' },
        events: {{ Illuminate\Support\Js::from($calendarEvents) }},
        displayEventTime: true,
        eventDidMount: function(info) { info.el.title = info.event.title; }
    }).render();
});
</script>
