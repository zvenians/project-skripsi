<?php

namespace App\Services;

use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class BookingConflictService
{
    /** @param array{facility_id:int,user_id:int,detil:string,date_end:string,selesai:string,status_order?:int|null,organization_id?:int|null} $attributes */
    public function create(array $attributes): Order
    {
        return DB::transaction(function () use ($attributes): Order {
            $this->assertAvailable((int) $attributes['facility_id'], $attributes['date_end'], $attributes['selesai']);

            return Order::query()->create($attributes);
        }, 3);
    }

    /** @param array<string,mixed> $attributes */
    public function update(Order $order, array $attributes): Order
    {
        return DB::transaction(function () use ($order, $attributes): Order {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->getKey());
            $facilityId = (int) ($attributes['facility_id'] ?? $locked->facility_id);
            $start = $attributes['date_end'] ?? $locked->date_end;
            $end = $attributes['selesai'] ?? $locked->selesai;
            $this->assertAvailable($facilityId, $start, $end, $locked->getKey());
            $locked->fill($attributes)->save();

            return $locked->refresh();
        }, 3);
    }

    public function approve(Order $order, string $reason): Order
    {
        return DB::transaction(function () use ($order, $reason): Order {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->getKey());
            $this->assertAvailable(
                (int) $locked->facility_id,
                $locked->date_end,
                $locked->selesai,
                $locked->getKey(),
            );
            $locked->forceFill([
                'status_order' => 2,
                'decision_reason' => $reason,
            ])->save();

            return $locked->refresh();
        }, 3);
    }

    public function assertAvailable(int $facilityId, string|\DateTimeInterface $start, string|\DateTimeInterface $end, ?int $ignoreId = null): void
    {
        $startsAt = CarbonImmutable::parse($start, config('app.timezone'));
        $endsAt = CarbonImmutable::parse($end, config('app.timezone'));

        $conflict = Order::query()
            ->where('facility_id', $facilityId)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where(fn ($query) => $query->whereNull('status_order')->orWhere('status_order', 2))
            ->where('date_end', '<', $endsAt)
            ->where('selesai', '>', $startsAt)
            ->lockForUpdate()
            ->with('facility:id,title')
            ->first();

        if ($conflict) {
            throw ValidationException::withMessages([
                'date_end' => sprintf(
                    '%s tidak tersedia pada %s–%s. Pilih waktu lain.',
                    $conflict->facility?->title ?? 'Fasilitas',
                    CarbonImmutable::parse($conflict->date_end)->format('d M Y H:i'),
                    CarbonImmutable::parse($conflict->selesai)->format('H:i'),
                ),
            ]);
        }
    }
}
