import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import './style.css';
import { createApp, nextTick } from 'vue/dist/vue.esm-bundler.js';
import {
  ArrowRight, ArrowUpRight, Building2, CalendarDays, CalendarPlus, CalendarX,
  Check, ChevronRight, CircleCheck, ClipboardList, Clock3, LayoutDashboard,
  Menu, MessageSquareText, MessagesSquare, Pencil, Plus, RotateCcw, Search,
  SearchX, Send, ShieldCheck, Trash2, Users, Wifi, X, createIcons,
} from 'lucide';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const icons = {
  ArrowRight, ArrowUpRight, Building2, CalendarDays, CalendarPlus, CalendarX,
  Check, ChevronRight, CircleCheck, ClipboardList, Clock3, LayoutDashboard,
  Menu, MessageSquareText, MessagesSquare, Pencil, Plus, RotateCcw, Search,
  SearchX, Send, ShieldCheck, Trash2, Users, Wifi, X,
};

const facilities = [
  { id: 1, name: 'NACB 101', category: 'Ruang Kelas', capacity: 80, image: '/photo/101.jpeg', description: 'Ruang pertemuan berpendingin udara untuk seminar, ibadah, dan kegiatan organisasi.' },
  { id: 2, name: 'NACB 102', category: 'Ruang Kelas', capacity: 60, image: '/photo/102.jpeg', description: 'Ruang kelas modern dengan proyektor dan tata kursi fleksibel.' },
  { id: 3, name: 'New Dining Hall', category: 'Aula', capacity: 350, image: '/photo/NDG.jpeg', description: 'Aula luas untuk acara kampus, pertemuan besar, dan kegiatan mahasiswa.' },
  { id: 4, name: 'Auditorium UNAI', category: 'Auditorium', capacity: 500, image: '/storage/facility-images/5y4WFXmphMQtBryjC60RSfpUXr8YgRYBOW2lmK7Q.png', description: 'Auditorium utama dengan panggung dan sistem audio untuk acara berskala besar.' },
  { id: 5, name: 'Lapangan Kampus', category: 'Olahraga', capacity: 250, image: '/storage/facility-images/SZWQ0q9jW1tuWOwzv41MtrK0EBLFNCuxeOhZKZX8.jpg', description: 'Area kegiatan luar ruang untuk olahraga dan acara komunitas kampus.' },
  { id: 6, name: 'Ruang Rapat', category: 'Rapat', capacity: 24, image: '/storage/facility-images/SbkgqtSp104aqr0UKGn20T7uL9ylu1ZzddIDJbYc.jpg', description: 'Ruang diskusi yang tenang untuk rapat pengurus dan kelompok kecil.' },
];

const defaultOrders = [
  { id: 1001, facilityId: 1, organization: 'BEM UNAI', purpose: 'Rapat koordinasi semester', start: '2026-11-20T09:00', end: '2026-11-20T11:00', status: 'Disetujui', createdAt: '18 Nov 2026' },
  { id: 1002, facilityId: 3, organization: 'Fakultas Teknologi Informasi', purpose: 'Seminar karier mahasiswa', start: '2026-11-24T13:00', end: '2026-11-24T16:00', status: 'Menunggu', createdAt: '18 Nov 2026' },
  { id: 1003, facilityId: 6, organization: 'Himpunan Mahasiswa', purpose: 'Evaluasi kegiatan organisasi', start: '2026-11-21T15:00', end: '2026-11-21T17:00', status: 'Ditolak', createdAt: '17 Nov 2026' },
];

const storageKey = 'fasilitas-unai-demo-v2';
const readOrders = () => {
  try { return JSON.parse(localStorage.getItem(storageKey)) || structuredClone(defaultOrders); }
  catch { return structuredClone(defaultOrders); }
};

createApp({
  data: () => ({
    page: 'login', entered: false, menuOpen: false, query: '', category: 'Semua', statusFilter: 'Semua',
    facilities, orders: readOrders(), toast: null, selectedFacility: null, editingOrder: null,
    form: { organization: '', facilityId: '', purpose: '', start: '', end: '' }, errors: {}, submitting: false,
  }),
  computed: {
    protectedPage() { return ['dashboard', 'facilities', 'reservations', 'calendar', 'suggestions'].includes(this.page); },
    filteredFacilities() {
      const q = this.query.trim().toLocaleLowerCase('id');
      return this.facilities.filter(f => (this.category === 'Semua' || f.category === this.category) && (!q || `${f.name} ${f.category} ${f.description}`.toLocaleLowerCase('id').includes(q)));
    },
    categories() { return ['Semua', ...new Set(this.facilities.map(f => f.category))]; },
    filteredOrders() { return this.statusFilter === 'Semua' ? this.orders : this.orders.filter(o => o.status === this.statusFilter); },
    approved() { return this.orders.filter(o => o.status === 'Disetujui'); },
    pending() { return this.orders.filter(o => o.status === 'Menunggu').length; },
  },
  methods: {
    facility(id) { return this.facilities.find(f => f.id === Number(id)); },
    route(page) {
      this.page = page; this.menuOpen = false;
      history.replaceState(null, '', `#/${page}`);
      if (page !== 'facilities') { this.query = ''; this.category = 'Semua'; }
      window.scrollTo({ top: 0, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
      this.enhance();
    },
    enterDemo() { this.entered = true; sessionStorage.setItem('unai-demo-entered', '1'); this.route('dashboard'); this.notify('Demo siap digunakan', 'Data tersimpan aman di browser ini.'); },
    openFacility(item) { this.selectedFacility = item; this.enhance(); },
    openReservation(item = null) {
      this.errors = {}; this.editingOrder = item || { isNew: true, status: 'Menunggu' };
      this.form = item ? { organization: item.organization, facilityId: item.facilityId, purpose: item.purpose, start: item.start, end: item.end } : { organization: '', facilityId: this.selectedFacility?.id || '', purpose: '', start: '', end: '' };
      this.selectedFacility = null; this.enhance();
    },
    validate() {
      const e = {};
      if (this.form.organization.trim().length < 3) e.organization = 'Nama organisasi minimal 3 karakter.';
      if (!this.form.facilityId) e.facilityId = 'Pilih fasilitas yang akan digunakan.';
      if (this.form.purpose.trim().length < 8) e.purpose = 'Jelaskan kegiatan minimal 8 karakter.';
      if (!this.form.start) e.start = 'Waktu mulai wajib diisi.';
      if (!this.form.end) e.end = 'Waktu selesai wajib diisi.';
      if (this.form.start && this.form.end && new Date(this.form.end) <= new Date(this.form.start)) e.end = 'Waktu selesai harus setelah waktu mulai.';
      this.errors = e; return Object.keys(e).length === 0;
    },
    async saveOrder() {
      if (!this.validate()) return;
      this.submitting = true; await new Promise(resolve => setTimeout(resolve, 420));
      const payload = { ...this.form, facilityId: Number(this.form.facilityId), status: this.editingOrder?.status || 'Menunggu' };
      if (this.editingOrder && !this.editingOrder.isNew) {
        this.orders = this.orders.map(o => o.id === this.editingOrder.id ? { ...o, ...payload } : o);
        this.notify('Pemesanan diperbarui', 'Perubahan data demo telah disimpan.');
      } else {
        this.orders.unshift({ ...payload, id: Date.now(), createdAt: new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date()) });
        this.notify('Pemesanan berhasil dibuat', 'Status awal pemesanan adalah Menunggu.');
      }
      this.persist(); this.submitting = false; this.editingOrder = null;
      this.form = { organization: '', facilityId: '', purpose: '', start: '', end: '' };
      this.route('reservations');
    },
    setStatus(order, status) { order.status = status; this.persist(); this.notify(`Pemesanan ${status.toLowerCase()}`, `${this.facility(order.facilityId)?.name} telah diperbarui.`); },
    removeOrder(order) { if (!confirm(`Hapus pemesanan ${this.facility(order.facilityId)?.name}?`)) return; this.orders = this.orders.filter(o => o.id !== order.id); this.persist(); this.notify('Pemesanan dihapus', 'Data demo berhasil dihapus.'); },
    resetDemo() { if (!confirm('Reset seluruh perubahan data demo?')) return; this.orders = structuredClone(defaultOrders); this.persist(); this.notify('Data demo direset', 'Data awal telah dipulihkan.'); },
    persist() { localStorage.setItem(storageKey, JSON.stringify(this.orders)); },
    notify(title, message) { this.toast = { title, message }; clearTimeout(this.toastTimer); this.toastTimer = setTimeout(() => this.toast = null, 3800); },
    formatDate(value) { return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value)); },
    enhance() {
      nextTick(() => {
        createIcons({ icons });
        if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        gsap.fromTo('[data-enter]', { opacity: 0, y: 14 }, { opacity: 1, y: 0, duration: .52, stagger: .055, ease: 'power2.out', clearProps: 'all' });
        ScrollTrigger.refresh();
      });
    },
    syncRoute() {
      const route = location.hash.replace('#/', '') || 'login';
      const allowed = ['login', 'home', 'dashboard', 'facilities', 'reservations', 'calendar', 'suggestions'];
      this.page = allowed.includes(route) ? route : 'login';
      if (this.protectedPage) this.entered = true;
      this.enhance();
    },
  },
  mounted() { this.entered = sessionStorage.getItem('unai-demo-entered') === '1'; this.syncRoute(); addEventListener('hashchange', this.syncRoute); },
  template: `
    <div class="app-shell" :class="{ 'is-dashboard': protectedPage }">
      <header v-if="!protectedPage" class="public-header">
        <a href="#/home" class="brand" @click.prevent="route('home')"><img src="/photo/logounai.png" alt="Logo UNAI"><span><strong>Fasilitas</strong><small>Universitas Advent Indonesia</small></span></a>
        <nav aria-label="Navigasi utama"><button class="nav-link" :class="{active:page==='home'}" @click="route('home')">Beranda</button><button class="nav-link" :class="{active:page==='facilities'}" @click="route('facilities')">Fasilitas</button><button class="btn btn-primary btn-sm" @click="route('login')">Masuk Demo</button></nav>
      </header>

      <aside v-if="protectedPage" class="sidebar" :class="{ open: menuOpen }">
        <div class="sidebar-brand"><img src="/photo/logounai.png" alt=""><span>Fasilitas <b>UNAI</b></span><button class="icon-button d-lg-none" aria-label="Tutup menu" @click="menuOpen=false"><i data-lucide="x"></i></button></div>
        <nav class="side-nav" aria-label="Dashboard">
          <button :class="{active:page==='dashboard'}" @click="route('dashboard')"><i data-lucide="layout-dashboard"></i>Ringkasan</button>
          <button :class="{active:page==='facilities'}" @click="route('facilities')"><i data-lucide="building-2"></i>Fasilitas</button>
          <button :class="{active:page==='reservations'}" @click="route('reservations')"><i data-lucide="clipboard-list"></i>Pemesanan <span v-if="pending" class="nav-count">{{ pending }}</span></button>
          <button :class="{active:page==='calendar'}" @click="route('calendar')"><i data-lucide="calendar-days"></i>Jadwal</button>
          <button :class="{active:page==='suggestions'}" @click="route('suggestions')"><i data-lucide="message-square-text"></i>Kritik & Saran</button>
        </nav>
        <div class="sidebar-foot"><div class="demo-user"><span>PD</span><div><strong>Pengunjung Demo</strong><small>Administrator sandbox</small></div></div><button class="reset-link" @click="resetDemo"><i data-lucide="rotate-ccw"></i>Reset data demo</button></div>
      </aside>
      <div v-if="protectedPage && menuOpen" class="sidebar-backdrop" @click="menuOpen=false"></div>

      <header v-if="protectedPage" class="dashboard-header"><button class="icon-button d-lg-none" aria-label="Buka menu" @click="menuOpen=true"><i data-lucide="menu"></i></button><div class="header-context"><strong>Mode Demo</strong><span>Data terisolasi di perangkat ini</span></div><button class="btn btn-primary btn-sm" @click="openReservation()"><i data-lucide="plus"></i><span class="d-none d-sm-inline">Pemesanan baru</span></button></header>

      <main id="main-content" :class="protectedPage ? 'dashboard-main' : 'public-main'">
        <section v-if="page==='login'" class="login-page">
          <div class="login-visual" data-enter><div class="visual-overlay"><span class="eyebrow light">UNIVERSITAS ADVENT INDONESIA</span><h1>Ruang untuk setiap gagasan dan kegiatan.</h1><p>Temukan, jadwalkan, dan kelola fasilitas kampus dari satu tempat.</p><div class="visual-stats"><span><b>6</b> Fasilitas</span><span><b>24/7</b> Akses demo</span></div></div></div>
          <div class="login-panel" data-enter><div class="login-card"><img src="/photo/logounai.png" class="login-logo" alt="Logo UNAI"><span class="eyebrow">PORTAL RESERVASI</span><h2>Selamat datang</h2><p class="muted">Jelajahi seluruh alur sebagai administrator demo. Tidak perlu email atau kata sandi.</p><div class="demo-notice"><i data-lucide="shield-check"></i><div><strong>Lingkungan sandbox</strong><span>Perubahan hanya tersimpan di browser Anda.</span></div></div><button class="btn btn-primary btn-lg w-100" @click="enterDemo">Masuk ke Demo <i data-lucide="arrow-right"></i></button><button class="text-button" @click="route('home')">Lihat halaman publik</button><p class="login-foot">Dengan melanjutkan, Anda menggunakan data simulasi non-operasional.</p></div></div>
        </section>

        <section v-else-if="page==='home'" class="home-page">
          <div class="hero" data-enter><img src="/photo/unaiiiii.jpg" alt="Kampus Universitas Advent Indonesia"><div class="hero-copy"><span class="eyebrow light">FASILITAS KAMPUS</span><h1>Pesan ruang kampus dengan lebih mudah.</h1><p>Satu portal untuk melihat ketersediaan, mengajukan pemakaian, dan memantau status reservasi fasilitas UNAI.</p><div class="hero-actions"><button class="btn btn-primary btn-lg" @click="route('login')">Coba mode demo</button><button class="btn btn-outline-light btn-lg" @click="route('facilities')">Lihat fasilitas</button></div></div></div>
          <div class="public-section"><div class="section-heading" data-enter><div><span class="eyebrow">PILIHAN FASILITAS</span><h2>Ruang yang mendukung kegiatan Anda</h2></div><button class="text-button" @click="route('facilities')">Lihat semua <i data-lucide="arrow-right"></i></button></div><div class="feature-grid"><article v-for="item in facilities.slice(0,3)" :key="item.id" class="feature-card" data-enter><img :src="item.image" :alt="item.name"><div><span>{{ item.category }}</span><h3>{{ item.name }}</h3><p>{{ item.description }}</p></div></article></div></div>
        </section>

        <section v-else-if="page==='dashboard'" class="content-page">
          <div class="page-heading" data-enter><div><span class="eyebrow">18 NOVEMBER 2026</span><h1>Selamat datang, Pengunjung Demo</h1><p>Berikut ringkasan aktivitas reservasi fasilitas hari ini.</p></div><button class="btn btn-primary" @click="openReservation()"><i data-lucide="plus"></i>Buat pemesanan</button></div>
          <div class="metric-grid"><article class="metric-card" data-enter><span class="metric-icon blue"><i data-lucide="building-2"></i></span><div><span>Fasilitas tersedia</span><strong>{{ facilities.length }}</strong><small>Semua dapat dipesan</small></div></article><article class="metric-card" data-enter><span class="metric-icon amber"><i data-lucide="clock-3"></i></span><div><span>Menunggu tinjauan</span><strong>{{ pending }}</strong><small>Perlu tindakan admin</small></div></article><article class="metric-card" data-enter><span class="metric-icon green"><i data-lucide="circle-check"></i></span><div><span>Disetujui</span><strong>{{ approved.length }}</strong><small>Jadwal aktif</small></div></article></div>
          <div class="dashboard-grid"><article class="panel" data-enter><div class="panel-head"><div><h2>Pemesanan terbaru</h2><p>Aktivitas sandbox Anda</p></div><button class="text-button" @click="route('reservations')">Lihat semua</button></div><div class="order-list"><div v-for="order in orders.slice(0,4)" :key="order.id" class="order-row"><img :src="facility(order.facilityId)?.image" alt=""><div><strong>{{ facility(order.facilityId)?.name }}</strong><span>{{ order.organization }} · {{ formatDate(order.start) }}</span></div><span class="status" :class="order.status.toLowerCase()">{{ order.status }}</span></div></div></article><article class="panel quick-panel" data-enter><div class="panel-head"><div><h2>Aksi cepat</h2><p>Kelola demo</p></div></div><button @click="openReservation()"><i data-lucide="calendar-plus"></i><span><strong>Ajukan pemakaian</strong><small>Buat reservasi baru</small></span><i data-lucide="chevron-right"></i></button><button @click="route('facilities')"><i data-lucide="search"></i><span><strong>Cari fasilitas</strong><small>Filter ruang dan kapasitas</small></span><i data-lucide="chevron-right"></i></button><button @click="route('calendar')"><i data-lucide="calendar-days"></i><span><strong>Lihat jadwal</strong><small>Pemakaian yang disetujui</small></span><i data-lucide="chevron-right"></i></button></article></div>
        </section>

        <section v-else-if="page==='facilities'" class="content-page">
          <div class="page-heading" data-enter><div><span class="eyebrow">DIREKTORI KAMPUS</span><h1>Fasilitas UNAI</h1><p>Temukan ruang yang sesuai dengan kapasitas dan kebutuhan kegiatan.</p></div></div>
          <div class="filter-bar" data-enter><label class="search-field"><i data-lucide="search"></i><input v-model="query" type="search" placeholder="Cari nama atau fasilitas…" aria-label="Cari fasilitas"></label><div class="filter-chips" aria-label="Filter kategori"><button v-for="item in categories" :key="item" :class="{active:category===item}" @click="category=item">{{ item }}</button></div></div>
          <p class="result-count" aria-live="polite">{{ filteredFacilities.length }} dari {{ facilities.length }} fasilitas</p>
          <div v-if="filteredFacilities.length" class="facility-grid"><article v-for="item in filteredFacilities" :key="item.id" class="facility-card" data-enter><button class="image-button" @click="openFacility(item)"><img :src="item.image" :alt="item.name"><span class="capacity"><i data-lucide="users"></i>{{ item.capacity }} orang</span></button><div class="facility-body"><span>{{ item.category }}</span><h2>{{ item.name }}</h2><p>{{ item.description }}</p><button class="text-button" @click="openFacility(item)">Lihat detail <i data-lucide="arrow-up-right"></i></button></div></article></div><div v-else class="empty-state" data-enter><i data-lucide="search-x"></i><h2>Fasilitas tidak ditemukan</h2><p>Coba kata kunci atau kategori yang berbeda.</p><button class="btn btn-outline-primary" @click="query='';category='Semua'">Reset filter</button></div>
        </section>

        <section v-else-if="page==='reservations'" class="content-page">
          <div class="page-heading" data-enter><div><span class="eyebrow">ADMINISTRASI</span><h1>Daftar pemesanan</h1><p>Kelola permintaan dan status penggunaan fasilitas demo.</p></div><button class="btn btn-primary" @click="openReservation()"><i data-lucide="plus"></i>Pemesanan baru</button></div>
          <div class="table-panel" data-enter><div class="table-tools"><div class="filter-chips"><button v-for="item in ['Semua','Menunggu','Disetujui','Ditolak']" :key="item" :class="{active:statusFilter===item}" @click="statusFilter=item">{{ item }}</button></div><span>{{ filteredOrders.length }} data</span></div><div class="responsive-table"><table><thead><tr><th>Fasilitas & kegiatan</th><th>Organisasi</th><th>Waktu</th><th>Status</th><th><span class="visually-hidden">Aksi</span></th></tr></thead><tbody><tr v-for="order in filteredOrders" :key="order.id"><td><div class="table-facility"><img :src="facility(order.facilityId)?.image" alt=""><div><strong>{{ facility(order.facilityId)?.name }}</strong><span>{{ order.purpose }}</span></div></div></td><td>{{ order.organization }}</td><td><strong>{{ formatDate(order.start) }}</strong><span class="cell-sub">s.d. {{ formatDate(order.end) }}</span></td><td><span class="status" :class="order.status.toLowerCase()">{{ order.status }}</span></td><td><div class="row-actions"><button v-if="order.status==='Menunggu'" title="Setujui" @click="setStatus(order,'Disetujui')"><i data-lucide="check"></i></button><button v-if="order.status==='Menunggu'" title="Tolak" @click="setStatus(order,'Ditolak')"><i data-lucide="x"></i></button><button title="Edit" @click="openReservation(order)"><i data-lucide="pencil"></i></button><button title="Hapus" class="danger" @click="removeOrder(order)"><i data-lucide="trash-2"></i></button></div></td></tr></tbody></table></div></div>
        </section>

        <section v-else-if="page==='calendar'" class="content-page"><div class="page-heading" data-enter><div><span class="eyebrow">JADWAL TERKONFIRMASI</span><h1>Kalender pemakaian</h1><p>Hanya pemesanan yang disetujui ditampilkan di sini.</p></div></div><div class="schedule-panel" data-enter><div class="calendar-date"><span>NOV</span><strong>2026</strong></div><div v-if="approved.length" class="schedule-list"><article v-for="order in approved" :key="order.id"><time><strong>{{ new Date(order.start).getDate() }}</strong><span>{{ new Intl.DateTimeFormat('id-ID',{weekday:'short'}).format(new Date(order.start)) }}</span></time><div><span class="schedule-time">{{ new Intl.DateTimeFormat('id-ID',{timeStyle:'short'}).format(new Date(order.start)) }} – {{ new Intl.DateTimeFormat('id-ID',{timeStyle:'short'}).format(new Date(order.end)) }}</span><h2>{{ facility(order.facilityId)?.name }}</h2><p>{{ order.purpose }} · {{ order.organization }}</p></div><span class="schedule-dot"></span></article></div><div v-else class="empty-state"><i data-lucide="calendar-x"></i><h2>Belum ada jadwal</h2><p>Setujui pemesanan untuk menampilkannya di kalender.</p></div></div></section>

        <section v-else-if="page==='suggestions'" class="content-page"><div class="page-heading" data-enter><div><span class="eyebrow">SUARA PENGGUNA</span><h1>Kritik & saran</h1><p>Kirim masukan untuk membantu peningkatan layanan fasilitas.</p></div></div><div class="suggestion-layout"><form class="panel suggestion-form" data-enter @submit.prevent="notify('Masukan diterima','Terima kasih atas saran Anda.'); $event.target.reset()"><label>Nama organisasi<input class="form-control" required minlength="3" placeholder="Contoh: BEM UNAI"></label><label>Kategori<select class="form-select" required><option>Fasilitas</option><option>Pelayanan</option><option>Sistem reservasi</option></select></label><label>Pesan<textarea class="form-control" required minlength="10" rows="5" placeholder="Tuliskan masukan secara jelas…"></textarea></label><button class="btn btn-primary" type="submit">Kirim masukan <i data-lucide="send"></i></button></form><aside class="suggestion-note" data-enter><i data-lucide="messages-square"></i><h2>Masukan Anda berarti</h2><p>Dalam mode demo, formulir memberi respons nyata tanpa mengirim data ke sistem operasional UNAI.</p></aside></div></section>
      </main>

      <div v-if="selectedFacility" class="modal-layer" @click.self="selectedFacility=null"><section class="detail-modal" role="dialog" aria-modal="true" aria-labelledby="facility-modal-title" data-enter><button class="modal-close" aria-label="Tutup" @click="selectedFacility=null"><i data-lucide="x"></i></button><img :src="selectedFacility.image" :alt="selectedFacility.name"><div><span class="eyebrow">{{ selectedFacility.category }}</span><h2 id="facility-modal-title">{{ selectedFacility.name }}</h2><p>{{ selectedFacility.description }}</p><div class="detail-meta"><span><i data-lucide="users"></i><b>{{ selectedFacility.capacity }}</b> kapasitas</span><span><i data-lucide="wifi"></i><b>Tersedia</b> fasilitas dasar</span></div><button class="btn btn-primary w-100" @click="openReservation()">Pesan fasilitas ini</button></div></section></div>

      <div v-if="editingOrder !== null || form.organization || form.facilityId || form.purpose || form.start || form.end" class="modal-layer" @click.self="editingOrder=null;form={organization:'',facilityId:'',purpose:'',start:'',end:''};errors={}"><form class="reservation-modal" role="dialog" aria-modal="true" aria-labelledby="reservation-modal-title" @submit.prevent="saveOrder" data-enter novalidate><div class="modal-head"><div><span class="eyebrow">DATA SANDBOX</span><h2 id="reservation-modal-title">{{ editingOrder?.isNew ? 'Pemesanan baru' : 'Edit pemesanan' }}</h2><p>Lengkapi detail penggunaan fasilitas.</p></div><button type="button" class="modal-close static" aria-label="Tutup" @click="editingOrder=null;form={organization:'',facilityId:'',purpose:'',start:'',end:''};errors={}"><i data-lucide="x"></i></button></div><div class="modal-form"><label>Nama organisasi<input v-model="form.organization" class="form-control" :class="{'is-invalid':errors.organization}" @blur="validate" placeholder="Nama organisasi"><span class="invalid-feedback">{{ errors.organization }}</span></label><label>Fasilitas<select v-model="form.facilityId" class="form-select" :class="{'is-invalid':errors.facilityId}" @change="validate"><option value="">Pilih fasilitas</option><option v-for="item in facilities" :value="item.id" :key="item.id">{{ item.name }} — {{ item.capacity }} orang</option></select><span class="invalid-feedback">{{ errors.facilityId }}</span></label><label>Kegiatan<textarea v-model="form.purpose" class="form-control" :class="{'is-invalid':errors.purpose}" @blur="validate" rows="3" placeholder="Jelaskan kegiatan"></textarea><span class="invalid-feedback">{{ errors.purpose }}</span></label><div class="form-grid"><label>Mulai<input v-model="form.start" type="datetime-local" class="form-control" :class="{'is-invalid':errors.start}" @change="validate"><span class="invalid-feedback">{{ errors.start }}</span></label><label>Selesai<input v-model="form.end" type="datetime-local" class="form-control" :class="{'is-invalid':errors.end}" @change="validate"><span class="invalid-feedback">{{ errors.end }}</span></label></div></div><div class="modal-actions"><button type="button" class="btn btn-light" @click="editingOrder=null;form={organization:'',facilityId:'',purpose:'',start:'',end:''};errors={}">Batal</button><button class="btn btn-primary" :disabled="submitting"><span v-if="submitting" class="spinner-border spinner-border-sm"></span>{{ submitting ? 'Menyimpan…' : 'Simpan pemesanan' }}</button></div></form></div>

      <transition name="toast"><div v-if="toast" class="app-toast" role="status"><span><i data-lucide="check"></i></span><div><strong>{{ toast.title }}</strong><p>{{ toast.message }}</p></div><button aria-label="Tutup notifikasi" @click="toast=null"><i data-lucide="x"></i></button></div></transition>
    </div>
  `,
}).mount('#app');
