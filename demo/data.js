const at = (days, hour, minute = 0) => {
  const date = new Date();
  date.setHours(hour, minute, 0, 0);
  date.setDate(date.getDate() + days);
  const pad = value => String(value).padStart(2, '0');
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
};

export const facilities = [
  { id: 1, name: 'NACB 101', category: 'Ruang Kelas', capacity: 80, managerId: 1, image: '/photo/101.jpeg', description: 'Ruang pertemuan berpendingin udara untuk seminar, ibadah, dan kegiatan organisasi.', master: true },
  { id: 2, name: 'NACB 102', category: 'Ruang Kelas', capacity: 60, managerId: 1, image: '/photo/102.jpeg', description: 'Ruang kelas modern dengan proyektor dan tata kursi fleksibel.', master: true },
  { id: 3, name: 'New Dining Hall', category: 'Aula', capacity: 350, managerId: 1, image: '/photo/NDG.jpeg', description: 'Aula luas untuk acara kampus, pertemuan besar, dan kegiatan mahasiswa.', master: true },
  { id: 4, name: 'Auditorium UNAI', category: 'Auditorium', capacity: 500, managerId: 2, image: '/storage/facility-images/5y4WFXmphMQtBryjC60RSfpUXr8YgRYBOW2lmK7Q.png', description: 'Auditorium utama dengan panggung dan sistem audio untuk acara berskala besar.', master: true },
  { id: 5, name: 'Lapangan Kampus', category: 'Olahraga', capacity: 250, managerId: 2, image: '/storage/facility-images/SZWQ0q9jW1tuWOwzv41MtrK0EBLFNCuxeOhZKZX8.jpg', description: 'Area kegiatan luar ruang untuk olahraga dan acara komunitas kampus.', master: true },
  { id: 6, name: 'Ruang Rapat', category: 'Rapat', capacity: 24, managerId: 2, image: '/storage/facility-images/SbkgqtSp104aqr0UKGn20T7uL9ylu1ZzddIDJbYc.jpg', description: 'Ruang diskusi yang tenang untuk rapat pengurus dan kelompok kecil.', master: true },
];

export const organizations = [
  { id: 1, name: 'BEM UNAI', code: 'BEM', status: 'Aktif' },
  { id: 2, name: 'Fakultas Teknologi Informasi', code: 'FTI', status: 'Aktif' },
  { id: 3, name: 'Himpunan Mahasiswa', code: 'HIMA', status: 'Aktif' },
];

export const managers = [
  { id: 1, name: 'Pengelola Gedung Akademik', facilities: [1, 2, 3] },
  { id: 2, name: 'Pengelola Fasilitas Umum', facilities: [4, 5, 6] },
];

export const seedOrders = [
  { id: 1001, facilityId: 1, organizationId: 1, purpose: 'Rapat koordinasi semester', start: at(1, 9), end: at(1, 11), status: 'Disetujui', reason: '', createdAt: new Date().toISOString() },
  { id: 1002, facilityId: 3, organizationId: 2, purpose: 'Seminar karier mahasiswa', start: at(3, 13), end: at(3, 16), status: 'Menunggu', reason: '', createdAt: new Date().toISOString() },
  { id: 1003, facilityId: 6, organizationId: 3, purpose: 'Evaluasi kegiatan organisasi', start: at(-2, 15), end: at(-2, 17), status: 'Ditolak', reason: 'Fasilitas sedang dalam perawatan.', createdAt: at(-4, 9) },
];

export const announcements = [
  { id: 1, title: 'Pemeliharaan fasilitas akhir pekan', body: 'Periksa kalender sebelum mengajukan pemakaian pada hari Sabtu.', publishedAt: new Date().toISOString() },
  { id: 2, title: 'Ketentuan pemakaian ruang', body: 'Organisasi wajib mengembalikan tata ruang setelah kegiatan selesai.', publishedAt: at(-7, 9) },
];

export const roleProfiles = {
  superadmin: { label: 'Superadmin', short: 'SA', identity: 'Administrator Sistem', organizationId: null, managerId: null },
  organization: { label: 'Admin Organisasi', short: 'AO', identity: 'BEM UNAI', organizationId: 1, managerId: null },
  manager: { label: 'Admin Pengelola', short: 'AP', identity: 'Pengelola Gedung Akademik', organizationId: null, managerId: 1 },
};

export const roleMenus = {
  superadmin: [
    ['dashboard', 'Dashboard Overview', 'layout-dashboard'], ['facilities', 'Manajemen Fasilitas', 'building-2'], ['reservations', 'Semua Pemesanan', 'clipboard-list'], ['calendar', 'Kalender Global', 'calendar-days'], ['organizations', 'Manajemen Organisasi', 'landmark'], ['managers', 'Manajemen Pengelola', 'badge-check'], ['users', 'Pengguna & Role', 'users'], ['announcements', 'Pengumuman / Kegiatan', 'megaphone'], ['suggestions', 'Kritik & Saran', 'message-square-text'], ['activity', 'Activity Logs', 'history'], ['reports', 'Laporan & Export', 'file-chart-column'], ['settings', 'Pengaturan', 'settings'],
  ],
  organization: [
    ['dashboard', 'Dashboard Organisasi', 'layout-dashboard'], ['facilities', 'Jelajahi Fasilitas', 'search'], ['create-booking', 'Buat Pemesanan', 'calendar-plus'], ['reservations', 'Pemesanan Saya', 'clipboard-list'], ['calendar', 'Kalender Ketersediaan', 'calendar-days'], ['history', 'Riwayat Pemakaian', 'history'], ['announcements', 'Pengumuman', 'megaphone'], ['suggestions', 'Kritik & Saran', 'message-square-text'], ['profile', 'Profil Organisasi', 'circle-user-round'],
  ],
  manager: [
    ['dashboard', 'Dashboard Pengelola', 'layout-dashboard'], ['facilities', 'Fasilitas Dikelola', 'building-2'], ['reservations', 'Permintaan Masuk', 'inbox'], ['calendar', 'Kalender Fasilitas', 'calendar-days'], ['approvals', 'Persetujuan Pemesanan', 'circle-check'], ['history', 'Riwayat Penggunaan', 'history'], ['reports', 'Laporan Fasilitas', 'file-chart-column'], ['announcements', 'Pengumuman Internal', 'megaphone'],
  ],
};
