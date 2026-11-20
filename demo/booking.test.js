import { describe, expect, it } from 'vitest';
import { findConflict } from './booking';

const active = { id: 1, facilityId: 1, start: '2026-11-20T09:00', end: '2026-11-20T11:00', status: 'Disetujui' };

describe('booking conflict prevention', () => {
  it('rejects overlapping time on the same facility', () => {
    expect(findConflict([active], { facilityId: 1, start: '2026-11-20T10:00', end: '2026-11-20T12:00' })).toEqual(active);
  });

  it('allows adjacent bookings', () => {
    expect(findConflict([active], { facilityId: 1, start: '2026-11-20T11:00', end: '2026-11-20T13:00' })).toBeNull();
  });

  it('allows overlap on another facility', () => {
    expect(findConflict([active], { facilityId: 2, start: '2026-11-20T10:00', end: '2026-11-20T12:00' })).toBeNull();
  });

  it.each(['Ditolak', 'Dibatalkan'])('ignores %s bookings', status => {
    expect(findConflict([{ ...active, status }], { facilityId: 1, start: '2026-11-20T10:00', end: '2026-11-20T12:00' })).toBeNull();
  });
});
