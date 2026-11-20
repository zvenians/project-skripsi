import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import './style.css';
import { createApp, nextTick } from 'vue/dist/vue.esm-bundler.js';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import {
  Activity, ArrowRight, ArrowUpRight, BadgeCheck, Ban, Building2, CalendarDays,
  CalendarPlus, Check, CircleAlert, CircleCheck, CircleUserRound, ClipboardList,
  Clock3, Download, Eye, FileChartColumn, History, Inbox, Landmark, LayoutDashboard,
  LogOut, Megaphone, Menu, MessageSquareDashed, MessageSquareText, PanelLeftClose,
  Pencil, Plus, RotateCcw, Search, Send, Settings, ShieldCheck, TriangleAlert, Users,
  X, createIcons,
} from 'lucide';
import { gsap } from 'gsap';
import { announcements as seedAnnouncements, facilities as masterFacilities, managers, organizations, roleMenus, roleProfiles, seedOrders } from './data';
import { canAccessOrder, durationLabel, findConflict } from './booking';

const storeKey = 'fasilitas-unai-professional-v1';
const icons = {
  Activity, ArrowRight, ArrowUpRight, BadgeCheck, Ban, Building2, CalendarDays,
  CalendarPlus, Check, CircleAlert, CircleCheck, CircleUserRound, ClipboardList,
  Clock3, Download, Eye, FileChartColumn, History, Inbox, Landmark, LayoutDashboard,
  LogOut, Megaphone, Menu, MessageSquareDashed, MessageSquareText, PanelLeftClose,
  Pencil, Plus, RotateCcw, Search, Send, Settings, ShieldCheck, TriangleAlert, Users, X,
};
const emptyForm = () => ({ organizationId: '', facilityId: '', purpose: '', start: '', end: '' });
const clone = value => JSON.parse(JSON.stringify(value));
const readState = () => {
  try { return { orders: clone(seedOrders), suggestions: [], activity: [], announcements: clone(seedAnnouncements), ...JSON.parse(localStorage.getItem(storeKey)) }; }
  catch { return { orders: clone(seedOrders), suggestions: [], activity: [], announcements: clone(seedAnnouncements) }; }
};

const app = createApp({
  components: { FullCalendar },
  data() {
    const stored = readState();
    return {
      role: sessionStorage.getItem('unai-role') || '', page: 'login', menuOpen: false, sidebarCollapsed: false,
      facilities: clone(masterFacilities), organizations, managers, orders: stored.orders, suggestions: stored.suggestions,
      activity: stored.activity, announcements: stored.announcements, toast: null, selectedOrder: null, selectedFacility: null,
      editingOrder: null, form: emptyForm(), errors: {}, submitting: false, search: '', statusFilter: 'Semua', sort: 'newest', currentPage: 1,
      calendarFacility: 'Semua', calendarOrganization: 'Semua', calendarStatus: 'Semua', suggestionForm: { category: 'Sistem reservasi', message: '' }, roleProfiles,
    };
  },
  computed: {
    profile() { return roleProfiles[this.role] || roleProfiles.superadmin; },
    menus() { return roleMenus[this.role] || []; },
    protectedPage() { return !!this.role && !['login', 'home'].includes(this.page); },
    pageLabel() { return this.menus.find(item => item[0] === this.page)?.[1] || 'Fasilitas UNAI'; },
    scopedFacilities() { return this.role === 'manager' ? this.facilities.filter(f => f.managerId === this.profile.managerId) : this.facilities; },
    scopedOrders() { return this.orders.filter(o => canAccessOrder(this.role, this.profile, o, this.facility(o.facilityId))); },
    pending() { return this.scopedOrders.filter(o => o.status === 'Menunggu').length; },
    approved() { return this.scopedOrders.filter(o => o.status === 'Disetujui').length; },
    filteredOrders() {
      const query = this.search.trim().toLocaleLowerCase('id');
      const rows = this.scopedOrders.filter(o => {
        const haystack = `${this.facility(o.facilityId)?.name} ${this.organization(o.organizationId)?.name} ${o.purpose}`.toLocaleLowerCase('id');
        return (this.statusFilter === 'Semua' || o.status === this.statusFilter) && (!query || haystack.includes(query));
      });
      return [...rows].sort((a, b) => this.sort === 'oldest' ? new Date(a.start) - new Date(b.start) : new Date(b.start) - new Date(a.start));
    },
    pagedOrders() { return this.filteredOrders.slice((this.currentPage - 1) * 6, this.currentPage * 6); },
    pageCount() { return Math.max(1, Math.ceil(this.filteredOrders.length / 6)); },
    calendarOrders() { return this.scopedOrders.filter(o => (this.calendarFacility === 'Semua' || Number(this.calendarFacility) === o.facilityId) && (this.calendarOrganization === 'Semua' || Number(this.calendarOrganization) === o.organizationId) && (this.calendarStatus === 'Semua' || this.calendarStatus === o.status)); },
    calendarOptions() {
      return {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin], initialView: 'dayGridMonth', locale: 'id', firstDay: 1,
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        buttonText: { today: 'Hari ini', month: 'Bulan', week: 'Minggu', day: 'Hari' }, height: 'auto', nowIndicator: true,
        events: this.calendarOrders.map(o => ({ id: String(o.id), title: `${this.facility(o.facilityId)?.name} · ${o.purpose}`, start: o.start, end: o.end, classNames: [`event-${o.status.toLowerCase()}`], extendedProps: { order: o } })),
        eventClick: info => { this.selectedOrder = info.event.extendedProps.order; this.enhance(); },
        dateClick: info => { if (this.role === 'organization') this.openReservation(null, info.dateStr); }, dayMaxEvents: 3,
      };
    },
    usageBars() {
      const total = Math.max(1, this.scopedOrders.length);
      return this.scopedFacilities.slice(0, 5).map(f => ({ ...f, count: this.scopedOrders.filter(o => o.facilityId === f.id).length })).map(item => ({ ...item, percent: Math.round(item.count / total * 100) }));
    },
  },
  methods: {
    facility(id) { return this.facilities.find(f => f.id === Number(id)); },
    organization(id) { return this.organizations.find(o => o.id === Number(id)); },
    formatDate(value) { return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short', timeZone: 'Asia/Makassar' }).format(new Date(value)); },
    durationLabel,
    route(page) { if (!this.role && !['login', 'home'].includes(page)) page = 'login'; this.page = page; this.menuOpen = false; this.search = ''; this.currentPage = 1; history.replaceState(null, '', `#/${page}`); window.scrollTo({ top: 0, behavior: 'smooth' }); if (page === 'create-booking') this.openReservation(); this.enhance(); },
    enterRole(role) { this.role = role; sessionStorage.setItem('unai-role', role); this.route('dashboard'); this.log('Masuk ke mode demo', roleProfiles[role].label); this.notify('Mode demo aktif', `Anda masuk sebagai ${roleProfiles[role].label}.`); },
    switchRole(role) { this.enterRole(role); },
    signOut() { this.role = ''; sessionStorage.removeItem('unai-role'); this.route('login'); },
    openReservation(order = null, date = '') {
      if (this.role === 'manager') return this.notify('Akses dibatasi', 'Pengelola meninjau permintaan, bukan membuat pemesanan.', 'warning');
      if (order && this.role === 'organization' && order.organizationId !== this.profile.organizationId) return;
      this.editingOrder = order || { isNew: true, status: 'Menunggu' };
      const start = date ? `${date}T09:00` : ''; const end = date ? `${date}T10:00` : '';
      this.form = order ? { organizationId: order.organizationId, facilityId: order.facilityId, purpose: order.purpose, start: order.start, end: order.end } : { organizationId: this.profile.organizationId || '', facilityId: this.selectedFacility?.id || '', purpose: '', start, end };
      this.errors = {}; this.selectedFacility = null; this.enhance();
    },
    validateBooking() {
      const errors = {};
      if (!this.form.organizationId) errors.organizationId = 'Organisasi wajib dipilih.';
      if (!this.form.facilityId) errors.facilityId = 'Fasilitas wajib dipilih.';
      if (this.form.purpose.trim().length < 8) errors.purpose = 'Nama kegiatan minimal 8 karakter.';
      if (!this.form.start) errors.start = 'Waktu mulai wajib diisi.';
      if (!this.form.end || new Date(this.form.end) <= new Date(this.form.start)) errors.end = 'Waktu selesai harus setelah waktu mulai.';
      const conflict = !Object.keys(errors).length ? findConflict(this.orders, { ...this.form, status: this.editingOrder?.status || 'Menunggu' }, this.editingOrder?.isNew ? null : this.editingOrder?.id) : null;
      if (conflict) errors.end = `${this.facility(conflict.facilityId)?.name} tidak tersedia pada ${this.formatDate(conflict.start)}–${this.formatDate(conflict.end)}. Pilih jam lain.`;
      this.errors = errors; return Object.keys(errors).length === 0;
    },
    async saveOrder() {
      if (!this.validateBooking()) return; this.submitting = true; await new Promise(resolve => setTimeout(resolve, 280));
      const payload = { ...this.form, organizationId: Number(this.form.organizationId), facilityId: Number(this.form.facilityId), status: this.editingOrder.status || 'Menunggu', reason: this.editingOrder.reason || '' };
      if (this.editingOrder.isNew) { const order = { ...payload, id: Date.now(), createdAt: new Date().toISOString() }; this.orders.unshift(order); this.log('Pemesanan dibuat', `${this.facility(order.facilityId).name} · ${order.purpose}`); this.notify('Pemesanan tersimpan', 'Permintaan baru berstatus Menunggu.'); }
      else { this.orders = this.orders.map(o => o.id === this.editingOrder.id ? { ...o, ...payload } : o); this.log('Pemesanan diperbarui', this.editingOrder.purpose); this.notify('Perubahan tersimpan', 'Jadwal dan detail pemesanan telah diperbarui.'); }
      this.closeBooking(); this.persist(); this.submitting = false; this.route('reservations');
    },
    closeBooking() { this.editingOrder = null; this.form = emptyForm(); this.errors = {}; },
    cancelOrder(order) { order.status = 'Dibatalkan'; order.reason = 'Dibatalkan oleh organisasi pemesan.'; this.log('Pemesanan dibatalkan', order.purpose); this.persist(); this.notify('Pemesanan dibatalkan', 'Slot kembali tersedia.', 'warning'); },
    decide(order, status) {
      if (!['manager', 'superadmin'].includes(this.role)) return;
      if (this.role === 'manager' && this.facility(order.facilityId)?.managerId !== this.profile.managerId) return this.notify('Akses ditolak', 'Fasilitas ini berada di luar penugasan Anda.', 'error');
      const reason = status === 'Ditolak' ? window.prompt('Tuliskan alasan penolakan:') : 'Disetujui oleh pengelola fasilitas.';
      if (status === 'Ditolak' && (!reason || reason.trim().length < 5)) return this.notify('Alasan diperlukan', 'Penolakan memerlukan alasan yang jelas.', 'warning');
      const conflict = status === 'Disetujui' ? findConflict(this.orders, { ...order, status }, order.id) : null;
      if (conflict) return this.notify('Persetujuan ditolak sistem', `Bentrok dengan ${this.organization(conflict.organizationId)?.name}, ${this.formatDate(conflict.start)}–${this.formatDate(conflict.end)}.`, 'error');
      order.status = status; order.reason = reason; this.log(`Pemesanan ${status.toLowerCase()}`, order.purpose); this.persist(); this.notify(`Status: ${status}`, reason, status === 'Ditolak' ? 'warning' : 'success');
    },
    submitSuggestion() { if (this.suggestionForm.message.trim().length < 10) return this.notify('Pesan terlalu singkat', 'Tuliskan masukan minimal 10 karakter.', 'warning'); this.suggestions.unshift({ id: Date.now(), role: this.profile.label, identity: this.profile.identity, ...this.suggestionForm, createdAt: new Date().toISOString() }); this.log('Kritik & saran dikirim', this.suggestionForm.category); this.suggestionForm = { category: 'Sistem reservasi', message: '' }; this.persist(); this.notify('Masukan tersimpan', 'Masukan sandbox dapat dibaca kembali setelah refresh.'); },
    exportCsv() { const rows = [['Fasilitas','Organisasi','Kegiatan','Mulai','Selesai','Status'], ...this.scopedOrders.map(o => [this.facility(o.facilityId)?.name, this.organization(o.organizationId)?.name, o.purpose, o.start, o.end, o.status])]; const blob = new Blob([rows.map(row => row.map(cell => `"${String(cell).replaceAll('"','""')}"`).join(',')).join('\n')], { type: 'text/csv' }); const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = 'laporan-fasilitas-unai.csv'; link.click(); URL.revokeObjectURL(link.href); this.log('Laporan diekspor', `${this.scopedOrders.length} pemesanan`); this.persist(); },
    log(action, detail) { this.activity.unshift({ id: Date.now() + Math.random(), action, detail, role: this.profile.label, at: new Date().toISOString() }); this.activity = this.activity.slice(0, 100); this.persist(); },
    persist() { localStorage.setItem(storeKey, JSON.stringify({ orders: this.orders, suggestions: this.suggestions, activity: this.activity, announcements: this.announcements })); },
    resetDemo() { if (!confirm('Reset seluruh perubahan sandbox? Data master fasilitas tetap dipertahankan.')) return; this.orders = clone(seedOrders); this.suggestions = []; this.activity = []; this.announcements = clone(seedAnnouncements); this.persist(); this.notify('Sandbox direset', 'Data master fasilitas tidak diubah.'); },
    notify(title, message, tone = 'success') { this.toast = { title, message, tone }; clearTimeout(this.toastTimer); this.toastTimer = setTimeout(() => this.toast = null, 4200); this.enhance(); },
    enhance() { nextTick(() => { createIcons({ icons }); const targets = document.querySelectorAll('[data-enter]'); if (!targets.length || matchMedia('(prefers-reduced-motion: reduce)').matches) return; gsap.fromTo(targets, { opacity: 0, y: 12 }, { opacity: 1, y: 0, duration: .42, stagger: .035, ease: 'power2.out', clearProps: 'all' }); }); },
    press(event) { if (!matchMedia('(prefers-reduced-motion: reduce)').matches) gsap.fromTo(event.currentTarget, { scale: .97 }, { scale: 1, duration: .35, ease: 'back.out(1.5)', clearProps: 'transform' }); },
    syncRoute() { const target = location.hash.replace('#/', '') || (this.role ? 'dashboard' : 'login'); this.page = target; if (!this.role && !['login','home'].includes(target)) this.page = 'login'; this.enhance(); },
  },
  mounted() { this.syncRoute(); addEventListener('hashchange', this.syncRoute); document.addEventListener('click', event => { const button = event.target.closest('button,.btn'); if (button) this.press({ currentTarget: button }); }); document.addEventListener('keydown', event => { if (event.key === 'Escape') { this.closeBooking(); this.selectedOrder = null; this.selectedFacility = null; } }); },
  template: `
    <div class="app-shell" :class="{ collapsed: sidebarCollapsed }">
      <header v-if="!protectedPage" class="public-header dark-nav"><a class="brand" href="#/home" @click.prevent="route('home')"><img src="/photo/logounai.png" alt="Logo UNAI"><span><strong>Fasilitas UNAI</strong><small>Portal Reservasi Kampus</small></span></a><nav><button @click="route('home')">Beranda</button><button @click="route('login')" class="gold-button">Masuk Demo</button></nav></header>
      <aside v-if="protectedPage" class="sidebar" :class="{open:menuOpen}"><div class="sidebar-brand"><img src="/photo/logounai.png" alt=""><span v-if="!sidebarCollapsed"><b>Fasilitas</b> UNAI</span><button class="icon-button desktop-collapse" @click="sidebarCollapsed=!sidebarCollapsed"><i data-lucide="panel-left-close"></i></button><button class="icon-button mobile-close" @click="menuOpen=false"><i data-lucide="x"></i></button></div><div v-if="!sidebarCollapsed" class="role-block"><span>{{ profile.short }}</span><div><b>{{ profile.label }}</b><small>{{ profile.identity }}</small></div></div><nav class="side-nav"><button v-for="item in menus" :key="item[0]" :class="{active:page===item[0]}" @click="route(item[0])" :title="item[1]"><i :data-lucide="item[2]"></i><span v-if="!sidebarCollapsed">{{ item[1] }}</span><em v-if="item[0]==='reservations'&&pending">{{ pending }}</em></button></nav><div class="sidebar-foot"><button @click="resetDemo"><i data-lucide="rotate-ccw"></i><span v-if="!sidebarCollapsed">Reset sandbox</span></button><button @click="signOut"><i data-lucide="log-out"></i><span v-if="!sidebarCollapsed">Keluar demo</span></button></div></aside><div v-if="menuOpen" class="backdrop" @click="menuOpen=false"></div>
      <header v-if="protectedPage" class="dashboard-header dark-nav"><button class="icon-button menu-trigger" @click="menuOpen=true"><i data-lucide="menu"></i></button><div><span>{{ pageLabel }}</span><small>{{ new Intl.DateTimeFormat('id-ID',{dateStyle:'full'}).format(new Date()) }}</small></div><div class="header-actions"><label class="role-switch"><span>Mode</span><select :value="role" @change="switchRole($event.target.value)"><option value="superadmin">Superadmin</option><option value="organization">Admin Organisasi</option><option value="manager">Admin Pengelola</option></select></label><button v-if="role!=='manager'" class="gold-button" @click="openReservation()"><i data-lucide="plus"></i><span>Pemesanan</span></button></div></header>
      <main id="main-content" :class="protectedPage?'dashboard-main':'public-main'">
        <section v-if="page==='login'" class="login-page"><div class="login-story" data-enter><img src="/photo/unaiiiii.jpg" alt="Kampus Universitas Advent Indonesia"><div><span class="kicker light">UNIVERSITAS ADVENT INDONESIA</span><h1>Kelola ruang kampus dengan tertib.</h1><p>Reservasi, persetujuan, dan kalender fasilitas dalam satu lingkungan demo terisolasi.</p></div></div><div class="login-panel" data-enter><div class="login-copy"><img src="/photo/logounai.png" alt="Logo UNAI"><span class="kicker">PILIH PENGALAMAN DEMO</span><h2>Masuk sesuai peran</h2><p>Tidak memerlukan email atau kata sandi. Setiap peran memiliki data dan kewenangan berbeda.</p></div><div class="role-grid"><button v-for="(profile,key) in roleProfiles" :key="key" @click="enterRole(key)"><span>{{ profile.short }}</span><div><b>{{ profile.label }}</b><small>{{ key==='superadmin'?'Akses menyeluruh':key==='organization'?'Pihak pemesan':'Pengelola fasilitas' }}</small></div><i data-lucide="arrow-up-right"></i></button></div><p class="sandbox-note"><i data-lucide="shield-check"></i> Seluruh perubahan tersimpan hanya di browser ini.</p></div></section>
        <section v-else-if="page==='home'" class="home-page"><div class="hero"><img src="/photo/unaiiiii.jpg" alt="Kampus UNAI"><div data-enter><span class="kicker light">FASILITAS KAMPUS</span><h1>Ruang yang tepat untuk kegiatan yang bermakna.</h1><p>Jelajahi fasilitas asli UNAI dan kelola pemakaian secara terstruktur.</p><button class="gold-button large" @click="route('login')">Buka demo tiga peran <i data-lucide="arrow-right"></i></button></div></div></section>
        <section v-else-if="page==='dashboard'" class="content-page"><div class="page-heading" data-enter><div><span class="kicker">{{ profile.label }}</span><h1>{{ profile.identity }}</h1><p>Ringkasan berdasarkan data sandbox aktif dan kewenangan peran Anda.</p></div></div><div class="metric-grid"><article data-enter v-for="metric in [[scopedFacilities.length,'Fasilitas dalam cakupan','building-2'],[pending,'Menunggu tindakan','clock-3'],[approved,'Disetujui','circle-check'],[scopedOrders.length,'Total pemesanan','activity']]" :key="metric[1]"><span><i :data-lucide="metric[2]"></i></span><div><small>{{ metric[1] }}</small><strong>{{ metric[0] }}</strong></div></article></div><div class="dashboard-grid"><article class="panel" data-enter><div class="panel-head"><div><span class="kicker">AKTIVITAS</span><h2>Pemesanan terbaru</h2></div><button @click="route('reservations')">Lihat semua</button></div><div class="order-list"><div v-for="order in scopedOrders.slice(0,5)" :key="order.id"><img :src="facility(order.facilityId)?.image" alt=""><div><b>{{ facility(order.facilityId)?.name }}</b><small>{{ organization(order.organizationId)?.name }} · {{ formatDate(order.start) }}</small></div><span class="status" :class="order.status.toLowerCase()">{{ order.status }}</span></div></div></article><article class="panel usage-panel" data-enter><div class="panel-head"><div><span class="kicker">PEMAKAIAN</span><h2>Distribusi fasilitas</h2></div></div><div class="usage-bars"><div v-for="item in usageBars" :key="item.id"><p><span>{{ item.name }}</span><b>{{ item.count }}</b></p><i><em :style="{width:Math.max(4,item.percent)+'%'}"></em></i></div></div></article></div></section>
        <section v-else-if="page==='facilities'" class="content-page"><div class="page-heading"><div><span class="kicker">DIREKTORI</span><h1>{{ role==='manager'?'Fasilitas dikelola':'Fasilitas UNAI' }}</h1><p>Data master dan seluruh aset asli dipertahankan.</p></div></div><div class="facility-grid"><article v-for="item in scopedFacilities" :key="item.id" data-enter><button class="facility-image" @click="selectedFacility=item;enhance()"><img :src="item.image" :alt="item.name"><span>{{ item.capacity }} orang</span></button><div><span class="kicker">{{ item.category }}</span><h2>{{ item.name }}</h2><p>{{ item.description }}</p><button @click="selectedFacility=item;enhance()">Lihat detail <i data-lucide="arrow-up-right"></i></button></div></article></div></section>
        <section v-else-if="['reservations','approvals','history'].includes(page)" class="content-page"><div class="page-heading"><div><span class="kicker">RESERVASI</span><h1>{{ pageLabel }}</h1><p>Data otomatis dibatasi sesuai organisasi atau fasilitas yang ditugaskan.</p></div><button v-if="role!=='manager'" class="gold-button" @click="openReservation()"><i data-lucide="plus"></i>Buat pemesanan</button></div><div class="table-panel"><div class="table-tools"><label><i data-lucide="search"></i><input v-model="search" @input="currentPage=1" placeholder="Cari fasilitas, organisasi, kegiatan…"></label><select v-model="statusFilter" @change="currentPage=1"><option>Semua</option><option>Menunggu</option><option>Disetujui</option><option>Ditolak</option><option>Dibatalkan</option></select><select v-model="sort"><option value="newest">Terbaru</option><option value="oldest">Terlama</option></select></div><div class="responsive-table"><table><thead><tr><th>Fasilitas & kegiatan</th><th>Organisasi</th><th>Jadwal</th><th>Status</th><th>Aksi</th></tr></thead><tbody><tr v-for="order in pagedOrders" :key="order.id"><td><div class="table-primary"><img :src="facility(order.facilityId)?.image" alt=""><div><b>{{ facility(order.facilityId)?.name }}</b><small>{{ order.purpose }}</small></div></div></td><td>{{ organization(order.organizationId)?.name }}</td><td><b>{{ formatDate(order.start) }}</b><small class="block">{{ durationLabel(order.start,order.end) }}</small></td><td><span class="status" :class="order.status.toLowerCase()">{{ order.status }}</span><small v-if="order.reason" class="reason">{{ order.reason }}</small></td><td><div class="row-actions"><button title="Detail" @click="selectedOrder=order;enhance()"><i data-lucide="eye"></i></button><button v-if="role==='organization'&&['Menunggu','Ditolak'].includes(order.status)" title="Ubah" @click="openReservation(order)"><i data-lucide="pencil"></i></button><button v-if="role==='organization'&&['Menunggu','Disetujui'].includes(order.status)" title="Batalkan" @click="cancelOrder(order)"><i data-lucide="ban"></i></button><button v-if="['manager','superadmin'].includes(role)&&order.status==='Menunggu'" class="approve" title="Setujui" @click="decide(order,'Disetujui')"><i data-lucide="check"></i></button><button v-if="['manager','superadmin'].includes(role)&&order.status==='Menunggu'" class="reject" title="Tolak" @click="decide(order,'Ditolak')"><i data-lucide="x"></i></button></div></td></tr><tr v-if="!pagedOrders.length"><td colspan="5"><div class="empty"><i data-lucide="inbox"></i><b>Belum ada data sesuai filter</b><span>Ubah pencarian atau buat pemesanan baru.</span></div></td></tr></tbody></table></div><div class="pagination"><button :disabled="currentPage===1" @click="currentPage--">Sebelumnya</button><span>Halaman {{ currentPage }} dari {{ pageCount }}</span><button :disabled="currentPage===pageCount" @click="currentPage++">Berikutnya</button></div></div></section>
        <section v-else-if="page==='calendar'" class="content-page"><div class="page-heading"><div><span class="kicker">KALENDER INTERAKTIF</span><h1>{{ pageLabel }}</h1><p>Klik event untuk detail; status aktif langsung memblokir slot.</p></div></div><div class="calendar-filters"><select v-model="calendarFacility"><option>Semua</option><option v-for="f in scopedFacilities" :value="f.id">{{ f.name }}</option></select><select v-if="role==='superadmin'" v-model="calendarOrganization"><option>Semua</option><option v-for="o in organizations" :value="o.id">{{ o.name }}</option></select><select v-model="calendarStatus"><option>Semua</option><option>Menunggu</option><option>Disetujui</option><option>Ditolak</option><option>Dibatalkan</option></select><div class="calendar-legend"><span>Menunggu</span><span>Disetujui</span><span>Ditolak</span></div></div><div class="calendar-panel"><FullCalendar :options="calendarOptions" /></div></section>
        <section v-else-if="page==='suggestions'" class="content-page"><div class="page-heading"><div><span class="kicker">MASUKAN</span><h1>Kritik & Saran</h1><p>Masukan disimpan nyata di sandbox dan tetap ada setelah refresh.</p></div></div><div class="suggestion-layout"><form class="panel editorial-form" @submit.prevent="submitSuggestion"><label>Kategori<select v-model="suggestionForm.category"><option>Fasilitas</option><option>Pelayanan</option><option>Sistem reservasi</option></select></label><label>Pesan<textarea v-model="suggestionForm.message" rows="6" placeholder="Tuliskan masukan dengan jelas…"></textarea></label><button class="gold-button">Simpan masukan <i data-lucide="send"></i></button></form><article class="panel"><div class="panel-head"><div><span class="kicker">TERSIMPAN</span><h2>Masukan terbaru</h2></div></div><div class="feedback-list"><div v-for="item in suggestions" :key="item.id"><span>{{ item.category }}</span><p>{{ item.message }}</p><small>{{ item.identity }} · {{ formatDate(item.createdAt) }}</small></div><div v-if="!suggestions.length" class="empty"><i data-lucide="message-square-dashed"></i><b>Belum ada masukan</b></div></div></article></div></section>
        <section v-else-if="page==='announcements'" class="content-page"><div class="page-heading"><div><span class="kicker">INFORMASI KAMPUS</span><h1>{{ pageLabel }}</h1></div></div><div class="announcement-grid"><article v-for="item in announcements" :key="item.id" class="panel"><span>{{ formatDate(item.publishedAt) }}</span><h2>{{ item.title }}</h2><p>{{ item.body }}</p></article></div></section>
        <section v-else class="content-page"><div class="page-heading"><div><span class="kicker">{{ profile.label }}</span><h1>{{ pageLabel }}</h1><p>Modul sandbox mengikuti batas akses {{ profile.identity }}.</p></div><button v-if="page==='reports'" class="gold-button" @click="exportCsv"><i data-lucide="download"></i>Export CSV</button></div><div class="module-grid"><article v-if="page==='organizations'" v-for="item in organizations" :key="'org-'+item.id" class="module-card"><span>{{ item.code }}</span><div><h2>{{ item.name }}</h2><p>Status organisasi: {{ item.status }}</p></div></article><article v-else-if="page==='managers'" v-for="item in managers" :key="'manager-'+item.id" class="module-card"><span>{{ item.facilities.length }}</span><div><h2>{{ item.name }}</h2><p>{{ item.facilities.map(id=>facility(id)?.name).join(', ') }}</p></div></article><article v-else-if="page==='activity'" v-for="item in activity" :key="'activity-'+item.id" class="activity-row"><span><i data-lucide="activity"></i></span><div><h2>{{ item.action }}</h2><p>{{ item.detail }} · {{ item.role }}</p></div><time>{{ formatDate(item.at) }}</time></article><article v-else-if="page==='profile'" key="profile" class="panel profile-card"><span>BEM</span><h2>BEM UNAI</h2><p>Organisasi pemesan aktif pada lingkungan sandbox.</p></article><article v-else key="summary" class="panel module-summary"><i :data-lucide="page==='settings'?'settings':page==='users'?'users':'file-chart-column'"></i><h2>{{ pageLabel }}</h2><p>Data pada modul ini mengikuti ringkasan aktif. Gunakan fitur tersedia tanpa mengubah data master fasilitas.</p></article></div></section>
      </main>
      <div v-if="editingOrder" class="modal-layer" @click.self="closeBooking"><form class="booking-modal" @submit.prevent="saveOrder"><header><div><span class="kicker">SANDBOX BOOKING</span><h2>{{ editingOrder.isNew?'Buat pemesanan':'Ubah / jadwalkan ulang' }}</h2></div><button type="button" @click="closeBooking"><i data-lucide="x"></i></button></header><div class="modal-body"><label>Organisasi<select v-model="form.organizationId" :disabled="role==='organization'" :class="{invalid:errors.organizationId}"><option value="">Pilih organisasi</option><option v-for="o in organizations" :value="o.id">{{ o.name }}</option></select><small v-if="errors.organizationId">{{ errors.organizationId }}</small></label><label>Fasilitas<select v-model="form.facilityId" :class="{invalid:errors.facilityId}"><option value="">Pilih fasilitas</option><option v-for="f in facilities" :value="f.id">{{ f.name }} — {{ f.capacity }} orang</option></select><small v-if="errors.facilityId">{{ errors.facilityId }}</small></label><label>Kegiatan<input v-model="form.purpose" :class="{invalid:errors.purpose}" placeholder="Nama kegiatan"><small v-if="errors.purpose">{{ errors.purpose }}</small></label><div class="form-grid"><label>Mulai<input v-model="form.start" type="datetime-local" :class="{invalid:errors.start}"><small v-if="errors.start">{{ errors.start }}</small></label><label>Selesai<input v-model="form.end" type="datetime-local" :class="{invalid:errors.end}"><small v-if="errors.end">{{ errors.end }}</small></label></div><div v-if="errors.end&&errors.end.includes('tidak tersedia')" class="conflict-alert"><i data-lucide="triangle-alert"></i><span>{{ errors.end }}</span></div></div><footer><button type="button" @click="closeBooking">Batal</button><button class="gold-button" :disabled="submitting">{{ submitting?'Menyimpan…':'Simpan pemesanan' }}</button></footer></form></div>
      <div v-if="selectedOrder" class="drawer-layer" @click.self="selectedOrder=null"><aside class="detail-drawer"><header><span class="kicker">DETAIL PEMESANAN</span><button @click="selectedOrder=null"><i data-lucide="x"></i></button></header><img :src="facility(selectedOrder.facilityId)?.image" alt=""><h2>{{ selectedOrder.purpose }}</h2><span class="status" :class="selectedOrder.status.toLowerCase()">{{ selectedOrder.status }}</span><dl><div><dt>Organisasi</dt><dd>{{ organization(selectedOrder.organizationId)?.name }}</dd></div><div><dt>Fasilitas</dt><dd>{{ facility(selectedOrder.facilityId)?.name }}</dd></div><div><dt>Mulai</dt><dd>{{ formatDate(selectedOrder.start) }}</dd></div><div><dt>Selesai</dt><dd>{{ formatDate(selectedOrder.end) }}</dd></div><div><dt>Durasi</dt><dd>{{ durationLabel(selectedOrder.start,selectedOrder.end) }}</dd></div><div v-if="selectedOrder.reason"><dt>Catatan keputusan</dt><dd>{{ selectedOrder.reason }}</dd></div></dl></aside></div>
      <div v-if="selectedFacility" class="modal-layer" @click.self="selectedFacility=null"><section class="facility-modal"><button @click="selectedFacility=null"><i data-lucide="x"></i></button><img :src="selectedFacility.image" :alt="selectedFacility.name"><div><span class="kicker">{{ selectedFacility.category }}</span><h2>{{ selectedFacility.name }}</h2><p>{{ selectedFacility.description }}</p><dl><div><dt>Kapasitas</dt><dd>{{ selectedFacility.capacity }} orang</dd></div><div><dt>Status master</dt><dd>Dilindungi</dd></div></dl><button v-if="role==='organization'" class="gold-button" @click="openReservation()">Pesan fasilitas</button></div></section></div>
      <transition name="toast"><div v-if="toast" class="app-toast" :class="toast.tone"><i :data-lucide="toast.tone==='error'?'circle-alert':toast.tone==='warning'?'triangle-alert':'circle-check'"></i><div><b>{{ toast.title }}</b><p>{{ toast.message }}</p></div><button @click="toast=null"><i data-lucide="x"></i></button></div></transition>
    </div>
  `,
});

app.config.errorHandler = (error, _instance, info) => console.error('Fasilitas UNAI UI error', info, error);
app.mount('#app');
