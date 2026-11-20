export const blockingStatuses = new Set(['Menunggu', 'Disetujui']);

export function findConflict(orders, candidate, ignoreId = null) {
  const start = new Date(candidate.start).getTime();
  const end = new Date(candidate.end).getTime();
  if (!Number.isFinite(start) || !Number.isFinite(end) || start >= end) return null;
  return orders.find(order =>
    order.id !== ignoreId &&
    Number(order.facilityId) === Number(candidate.facilityId) &&
    blockingStatuses.has(order.status) &&
    start < new Date(order.end).getTime() &&
    end > new Date(order.start).getTime()
  ) || null;
}

export function canAccessOrder(role, profile, order, facility) {
  if (role === 'superadmin') return true;
  if (role === 'organization') return order.organizationId === profile.organizationId;
  return facility?.managerId === profile.managerId;
}

export function durationLabel(start, end) {
  const minutes = Math.max(0, Math.round((new Date(end) - new Date(start)) / 60000));
  const hours = Math.floor(minutes / 60);
  const rest = minutes % 60;
  return [hours ? `${hours} jam` : '', rest ? `${rest} menit` : ''].filter(Boolean).join(' ') || '0 menit';
}
