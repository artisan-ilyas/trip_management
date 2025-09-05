<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'name'         => 'Tech Solutions',
                'legal_name'   => 'Tech Solutions Pvt Ltd',
                'slug'         => 'tech-solutions',
                'currency'     => 'USD',
                'timezone'     => 'America/New_York',
                'billing_email'=> 'billing@techsolutions.com',
                'address'      => '123 Silicon Valley, California, USA',
                'vat_tax_id'   => 'US123456789',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Global Traders',
                'legal_name'   => 'Global Traders Ltd',
                'slug'         => 'global-traders',
                'currency'     => 'EUR',
                'timezone'     => 'Europe/Berlin',
                'billing_email'=> 'accounts@globaltraders.com',
                'address'      => '45 Hauptstrasse, Berlin, Germany',
                'vat_tax_id'   => 'DE987654321',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
