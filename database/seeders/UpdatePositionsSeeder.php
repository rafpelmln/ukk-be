<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;

class UpdatePositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'slug' => 'guest',
                'name' => 'Tamu',
            ],
            [
                'slug' => 'anggota',
                'name' => 'Anggota',
            ],
            [
                'slug' => 'pengurus_wilayah',
                'name' => 'Pengurus Wilayah',
            ],
            [
                'slug' => 'pengurus_pusat',
                'name' => 'Pengurus Pusat',
            ],
        ];

        foreach ($positions as $positionData) {
            Position::where('slug', $positionData['slug'])
                ->update(['name' => $positionData['name']]);
        }
    }
}
