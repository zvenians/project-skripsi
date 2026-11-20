<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Order;
use App\Models\User;
use App\Services\BookingConflictService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BookingConflictTest extends TestCase
{
    use RefreshDatabase;

    private BookingConflictService $service;

    private User $user;

    private Facility $facility;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(BookingConflictService::class);
        $this->user = User::factory()->create();
        $this->facility = Facility::query()->create([
            'title' => 'Auditorium',
            'category' => 'Auditorium',
            'desc' => 'Fasilitas pengujian',
            'is_master' => true,
        ]);
    }

    public function test_overlapping_pending_booking_is_rejected(): void
    {
        $this->createBooking('2026-11-20 09:00:00', '2026-11-20 11:00:00');

        $this->expectException(ValidationException::class);
        $this->createBooking('2026-11-20 10:00:00', '2026-11-20 12:00:00');
    }

    public function test_adjacent_booking_is_allowed(): void
    {
        $this->createBooking('2026-11-20 09:00:00', '2026-11-20 11:00:00');
        $second = $this->createBooking('2026-11-20 11:00:00', '2026-11-20 12:00:00');

        $this->assertDatabaseHas('orders', ['id' => $second->id]);
    }

    public function test_rejected_booking_does_not_block_the_slot(): void
    {
        $this->createBooking('2026-11-20 09:00:00', '2026-11-20 11:00:00', 1);
        $replacement = $this->createBooking('2026-11-20 09:30:00', '2026-11-20 10:30:00');

        $this->assertDatabaseHas('orders', ['id' => $replacement->id]);
    }

    public function test_approved_booking_blocks_the_slot(): void
    {
        $this->createBooking('2026-11-20 09:00:00', '2026-11-20 11:00:00', 2);

        $this->expectException(ValidationException::class);
        $this->createBooking('2026-11-20 09:30:00', '2026-11-20 10:30:00');
    }

    public function test_pending_booking_can_be_approved_transactionally(): void
    {
        $order = $this->createBooking('2026-11-20 13:00:00', '2026-11-20 14:00:00');

        $approved = $this->service->approve($order, 'Jadwal dan fasilitas tersedia.');

        $this->assertSame(2, $approved->status_order);
        $this->assertSame('Jadwal dan fasilitas tersedia.', $approved->decision_reason);
    }

    private function createBooking(string $start, string $end, ?int $status = null): Order
    {
        return $this->service->create([
            'facility_id' => $this->facility->id,
            'user_id' => $this->user->id,
            'detil' => 'Kegiatan pengujian',
            'date_end' => $start,
            'selesai' => $end,
            'status_order' => $status,
        ]);
    }
}
