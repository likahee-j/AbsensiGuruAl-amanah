const puppeteer = require('puppeteer-core');
const fs = require('fs');

const EDGE = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
const BASE = 'http://127.0.0.1:8000';
const OUT = 'C:\\Users\\vivid\\AppData\\Local\\Temp\\absensi_shots';
if (!fs.existsSync(OUT)) fs.mkdirSync(OUT, { recursive: true });

async function login(page, email, pass) {
  await page.goto(BASE + '/login', { waitUntil: 'networkidle2' });
  await page.type('#email', email);
  await page.type('#password', pass);
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle2' }),
    page.click('button[type=submit]'),
  ]);
}

async function shot(page, path, name) {
  await page.goto(BASE + path, { waitUntil: 'networkidle2' });
  await new Promise(r => setTimeout(r, 800));
  await page.screenshot({ path: OUT + '\\' + name + '.png', fullPage: true });
  console.log('shot: ' + name + ' <- ' + path);
}

(async () => {
  const browser = await puppeteer.launch({
    executablePath: EDGE,
    headless: 'new',
    args: ['--no-sandbox', '--window-size=1366,900'],
    defaultViewport: { width: 1366, height: 900 },
  });
  const page = await browser.newPage();
  page.on('pageerror', e => console.log('  JS-ERR: ' + e.message));

  await login(page, 'admin@sekolah.sch.id', 'password123');
  await shot(page, '/admin', 'admin-dashboard');
  await shot(page, '/admin/pengguna/admin', 'pengguna-admin');
  await shot(page, '/admin/pengguna/guru', 'pengguna-guru');
  await shot(page, '/admin/pengguna/guru/6', 'pengguna-show');
  await shot(page, '/admin/pengguna/admin/create', 'pengguna-create');
  await shot(page, '/admin/sekolah', 'data-sekolah');
  await shot(page, '/admin/tapel', 'data-tapel');
  await shot(page, '/admin/libur', 'data-libur');
  await shot(page, '/admin/absensi', 'kelola-absensi');
  await shot(page, '/admin/rekap', 'rekapitulasi');
  await shot(page, '/admin/qr', 'qr-absensi');
  await shot(page, '/profile', 'profil');

  const page2 = await browser.newPage();
  page2.on('pageerror', e => console.log('  JS-ERR(guru): ' + e.message));
  await login(page2, 'budi.santoso@sekolah.sch.id', 'password123');
  await shot(page2, '/absensi', 'guru-absensi');
  await shot(page2, '/riwayat', 'guru-riwayat');

  await browser.close();
  console.log('ALL DONE -> ' + OUT);
})().catch(e => { console.error('FAIL', e); process.exit(1); });
