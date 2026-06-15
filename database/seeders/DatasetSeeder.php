<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatasetSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('datasets')->insert([
            [
    'user_id'         => 1,
    'title'           => 'Survey Gadget 2026',
    'class'           => 'Teknologi',
    'thumbnail'       => '',
    'description'     => 'Dataset hasil survei penggunaan gadget seperti smartphone, laptop, dan smartwatch.',
    'file_path'       => '',
    'points_required' => 0,
    'present_count'   => 430,
    'created_at'      => now(),
    'updated_at'      => now(),
],
[
    'user_id'         => 1,
    'title'           => 'Perlengkapan Olahraga',
    'class'           => 'Olahraga',
    'thumbnail'       => '',
    'description'     => 'Dataset survei minat masyarakat terhadap perlengkapan olahraga.',
    'file_path'       => '',
    'points_required' => 0,
    'present_count'   => 7000,
    'created_at'      => now(),
    'updated_at'      => now(),
],
[
    'user_id'         => 1,
    'title'           => 'Survey Teknologi Jaringan',
    'class'           => 'Teknologi',
    'thumbnail'       => '',
    'description'     => 'Dataset mengenai penggunaan teknologi jaringan di Indonesia.',
    'file_path'       => '',
    'points_required' => 0,
    'present_count'   => 10000,
    'created_at'      => now(),
    'updated_at'      => now(),
],
[
    'user_id'         => 1,
    'title'           => 'Survey Otomotif 2026',
    'class'           => 'Otomotif',
    'thumbnail'       => '',
    'description'     => 'Dataset tren otomotif dan kendaraan di Indonesia tahun 2026.',
    'file_path'       => '',
    'points_required' => 0,
    'present_count'   => 1000,
    'created_at'      => now(),
    'updated_at'      => now(),
],
        ]);
    }
}
