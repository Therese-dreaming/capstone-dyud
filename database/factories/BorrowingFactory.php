<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Borrowing>
 */
class BorrowingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Electronics & IT Equipments',
            'Fixtures',
            'Furnitures',
            'Religious or Institutional Items',
            'Teaching & Presentation Tools'
        ];
        
        $categoryItems = [
            'Electronics & IT Equipments' => ['laptop', 'projector', 'camera', 'microphone'],
            'Fixtures' => ['lamp', 'fan', 'whiteboard'],
            'Furnitures' => ['chair', 'table', 'cabinet'],
            'Religious or Institutional Items' => ['cross', 'flag', 'banner'],
            'Teaching & Presentation Tools' => ['pointer', 'markers', 'flipchart']
        ];
        
        $selectedCategory = $this->faker->randomElement($categories);
        $borrowDate = $this->faker->dateTimeBetween('-30 days', 'now');
        $dueDate = Carbon::parse($borrowDate)->addDays($this->faker->numberBetween(1, 14));
        
        return [
            'borrower_name' => $this->faker->name(),
            'borrower_id_number' => $this->faker->unique()->numerify('2021-###'),
            'room' => 'Room ' . $this->faker->numberBetween(100, 500),
            'category' => $selectedCategory,
            'items' => $this->faker->randomElements($categoryItems[$selectedCategory], $this->faker->numberBetween(1, 3)),
            'purpose' => $this->faker->sentence(),
            'borrow_date' => $borrowDate,
            'borrow_time' => $this->faker->time(),
            'due_date' => $dueDate,
            'return_date' => $this->faker->optional(0.3)->dateTimeBetween($borrowDate, $dueDate),
            'status' => $this->faker->randomElement(['active', 'returned', 'overdue']),
        ];
    }
    
    /**
     * Indicate that the borrowing is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'return_date' => null,
        ]);
    }
    
    /**
     * Indicate that the borrowing is returned.
     */
    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'returned',
            'return_date' => $this->faker->dateTimeBetween($attributes['borrow_date'], $attributes['due_date']),
        ]);
    }
    
    /**
     * Indicate that the borrowing is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'due_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
            'return_date' => null,
        ]);
    }
}
