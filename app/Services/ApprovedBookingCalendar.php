<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

final class ApprovedBookingCalendar
{
    /** @return array<int, array{title: string, start: string, end: string}> */
    public function events(string $selectedUser = 'all'): array
    {
        $query = Order::query()->with(['facility:id,title', 'user:id,name'])->where('status_order', 2);
        if ($selectedUser !== 'all') {
            $query->where('user_id', (int) $selectedUser);
        }

        return $query->get()->filter(fn (Order $order) => $order->date_end && $order->selesai)->map(static function (Order $order) {
            $name = $order->user?->name ?? 'Organisasi';
            $location = $order->facility?->title ?? 'Fasilitas';
            return [
                'title' => $location.' · '.$name,
                'start' => (string) $order->date_end,
                'end' => (string) $order->selesai,
            ];
        })->values()->all();
    }
}
