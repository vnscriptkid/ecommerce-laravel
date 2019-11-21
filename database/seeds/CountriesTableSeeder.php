<?php

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $string = File::get('D:\\workspace\\laragon\\www\\cart\\database\\seeds\\countries.json');
        $countries = json_decode($string, true);

        Country::insert($countries);
    }
}
