<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('user_can_print', '1', 'boolean');
        Setting::set('site_name', 'AutoTerra', 'text');
        Setting::set('site_email', 'support@autoterra.net', 'text');
        Setting::set('company_address', 'F-2104, 1st Floor, Tower B, Ardent Office One, Hoodi, Bangalore 560048, Karnataka, India', 'text');
    }
}
