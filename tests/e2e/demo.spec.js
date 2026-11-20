import { expect, test } from '@playwright/test';

async function openMobileMenu(page) {
  const trigger = page.locator('.menu-trigger');
  if (await trigger.isVisible()) await trigger.click();
}

test.beforeEach(async ({ page }) => {
  await page.goto('/#/login');
  await page.evaluate(() => { localStorage.clear(); sessionStorage.clear(); });
  await page.reload();
});

for (const role of ['Superadmin', 'Admin Organisasi', 'Admin Pengelola']) {
  test(`enters ${role} without credentials`, async ({ page }) => {
    await page.getByRole('button', { name: new RegExp(role) }).click();
    await expect(page.getByText(role).first()).toBeVisible();
    await expect(page.getByRole('heading').first()).toBeVisible();
  });
}

test('organization blocks overlap and accepts adjacent booking', async ({ page }) => {
  await page.getByRole('button', { name: /Admin Organisasi/ }).click();
  await page.locator('.dashboard-header .gold-button').click();
  const tomorrow = new Date(); tomorrow.setDate(tomorrow.getDate() + 1);
  const date = tomorrow.toISOString().slice(0, 10);
  await page.getByLabel('Fasilitas').selectOption('1');
  await page.getByLabel('Kegiatan').fill('Kegiatan yang bentrok');
  await page.getByLabel('Mulai').fill(`${date}T10:00`);
  await page.getByLabel('Selesai').fill(`${date}T12:00`);
  await page.getByRole('button', { name: 'Simpan pemesanan' }).click();
  await expect(page.locator('.conflict-alert')).toContainText('tidak tersedia');
  await page.getByLabel('Mulai').fill(`${date}T11:00`);
  await page.getByLabel('Selesai').fill(`${date}T13:00`);
  await page.getByRole('button', { name: 'Simpan pemesanan' }).click();
  await expect(page.getByText('Kegiatan yang bentrok')).toBeVisible();
  await page.reload();
  await expect(page.getByText('Kegiatan yang bentrok')).toBeVisible();
});

test('calendar exposes month week day and event details', async ({ page }) => {
  await page.getByRole('button', { name: /Superadmin/ }).click();
  await openMobileMenu(page);
  await page.getByRole('button', { name: /Kalender Global/ }).click();
  await expect(page.locator('.fc-dayGridMonth-button')).toBeVisible();
  await expect(page.locator('.fc-timeGridWeek-button')).toBeVisible();
  await expect(page.locator('.fc-timeGridDay-button')).toBeVisible();
  await page.locator('.fc-event').first().click();
  await expect(page.getByText('DETAIL PEMESANAN')).toBeVisible();
});

test('all role menus navigate without console errors', async ({ page }) => {
  test.setTimeout(60_000);
  const errors = []; page.on('console', msg => { if (msg.type() === 'error') errors.push(msg.text()); });
  await page.getByRole('button', { name: /Superadmin/ }).click();
  const menuCount = await page.locator('.side-nav button').count();
  for (let index = 0; index < menuCount; index += 1) {
    await openMobileMenu(page);
    await page.locator('.side-nav button').nth(index).click();
    await expect(page.locator('#main-content')).toBeVisible();
  }
  expect(errors).toEqual([]);
});
