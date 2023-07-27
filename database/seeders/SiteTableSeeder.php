<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $sites = [
            [
                'name'  => '-',
                'bu_id' => 1
            ]
        ];

        Site::insert($sites);
    }
}
