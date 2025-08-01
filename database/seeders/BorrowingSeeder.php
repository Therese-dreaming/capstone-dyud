<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Borrowing;
use Carbon\Carbon;

class BorrowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleBorrowings = [
            [
                'borrower_name' => 'John Doe',
                'borrower_id_number' => '2021-001',
                'room' => 'Room 101',
                'category' => 'Electronics & IT Equipments',
                'items' => ['laptop', 'projector'],
                'purpose' => 'Class presentation for Computer Science',
                'borrow_date' => Carbon::now()->subDays(2),
                'borrow_time' => '09:00:00',
                'due_date' => Carbon::now()->addDays(3),
                'status' => 'active'
            ],
            [
                'borrower_name' => 'Jane Smith',
                'borrower_id_number' => '2021-002',
                'room' => 'Room 205',
                'category' => 'Teaching & Presentation Tools',
                'items' => ['pointer', 'markers'],
                'purpose' => 'Faculty meeting presentation',
                'borrow_date' => Carbon::now()->subDays(5),
                'borrow_time' => '14:30:00',
                'due_date' => Carbon::now()->subDays(1),
                'status' => 'overdue'
            ],
            [
                'borrower_name' => 'Mike Johnson',
                'borrower_id_number' => '2021-003',
                'room' => 'Room 150',
                'category' => 'Furnitures',
                'items' => ['chair', 'table'],
                'purpose' => 'Event setup for student orientation',
                'borrow_date' => Carbon::now()->subDays(10),
                'borrow_time' => '08:00:00',
                'due_date' => Carbon::now()->subDays(3),
                'return_date' => Carbon::now()->subDays(3),
                'status' => 'returned'
            ],
            [
                'borrower_name' => 'Sarah Wilson',
                'borrower_id_number' => '2021-004',
                'room' => 'Room 301',
                'category' => 'Fixtures',
                'items' => ['lamp', 'fan'],
                'purpose' => 'Study session in library',
                'borrow_date' => Carbon::now()->subDays(1),
                'borrow_time' => '16:00:00',
                'due_date' => Carbon::now()->addDays(2),
                'status' => 'active'
            ],
            [
                'borrower_name' => 'David Brown',
                'borrower_id_number' => '2021-005',
                'room' => 'Room 402',
                'category' => 'Religious or Institutional Items',
                'items' => ['cross', 'flag'],
                'purpose' => 'Religious ceremony preparation',
                'borrow_date' => Carbon::now()->subDays(7),
                'borrow_time' => '10:00:00',
                'due_date' => Carbon::now()->subDays(2),
                'return_date' => Carbon::now()->subDays(2),
                'status' => 'returned'
            ]
        ];

        foreach ($sampleBorrowings as $borrowing) {
            Borrowing::create($borrowing);
        }
    }
}
