<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\Place;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- PLACES ----------
        $govt = Place::create([
            'name' => 'City Municipal Office',
            'type' => 'government',
            'address' => 'Main Road, City Center',
            'phone' => '01234-567890',
        ]);

        $bank = Place::create([
            'name' => 'National Bank - Main Branch',
            'type' => 'bank',
            'address' => 'Station Road, City Center',
            'phone' => '01234-111222',
        ]);

        // ---------- ADMIN ----------
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@smartqueue.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'phone' => '9000000001',
        ]);

        // ---------- SERVICES ----------
        $govtServices = [
            ['name' => 'Aadhaar Card', 'code' => 'ADH', 'est_minutes' => 8],
            ['name' => 'Birth Certificate', 'code' => 'BC', 'est_minutes' => 10],
            ['name' => 'Death Certificate', 'code' => 'DC', 'est_minutes' => 10],
            ['name' => 'Ration Card', 'code' => 'RC', 'est_minutes' => 12],
            ['name' => 'Property Tax', 'code' => 'PT', 'est_minutes' => 15],
        ];

        $bankServices = [
            ['name' => 'Deposit', 'code' => 'DEP', 'est_minutes' => 6],
            ['name' => 'Withdrawal', 'code' => 'WDL', 'est_minutes' => 6],
            ['name' => 'Account Opening', 'code' => 'AOP', 'est_minutes' => 20],
            ['name' => 'Loan Enquiry', 'code' => 'LON', 'est_minutes' => 15],
            ['name' => 'Card Service', 'code' => 'CRD', 'est_minutes' => 10],
        ];

        $govtSvcModels = collect($govtServices)->map(fn ($s) => $govt->services()->create($s));
        $bankSvcModels = collect($bankServices)->map(fn ($s) => $bank->services()->create($s));

        // ---------- STAFF ----------
        $staffData = [
            ['name' => 'Ramesh Kumar', 'email' => 'ramesh@smartqueue.test', 'phone' => '9000000002', 'place' => $govt, 'service' => 0, 'counter' => 'Window 1'],
            ['name' => 'Suresh Patel', 'email' => 'suresh@smartqueue.test', 'phone' => '9000000003', 'place' => $govt, 'service' => 1, 'counter' => 'Window 2'],
            ['name' => 'Anita Sharma', 'email' => 'anita@smartqueue.test', 'phone' => '9000000004', 'place' => $govt, 'service' => 2, 'counter' => 'Window 3'],
            ['name' => 'Vijay Mehta', 'email' => 'vijay@smartqueue.test', 'phone' => '9000000005', 'place' => $bank, 'service' => 0, 'counter' => 'Counter A'],
            ['name' => 'Priya Nair', 'email' => 'priya@smartqueue.test', 'phone' => '9000000006', 'place' => $bank, 'service' => 1, 'counter' => 'Counter B'],
        ];

        foreach ($staffData as $row) {
            $services = $row['place']->id === $govt->id ? $govtSvcModels : $bankSvcModels;
            $service = $services[$row['service']];

            $staff = User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make('password'),
                'role' => User::ROLE_STAFF,
                'phone' => $row['phone'],
                'place_id' => $row['place']->id,
            ]);

            Counter::create([
                'place_id' => $row['place']->id,
                'service_id' => $service->id,
                'staff_id' => $staff->id,
                'name' => $row['counter'],
                'location' => 'Ground Floor',
            ]);
        }

        // Extra unstaffed counters so users see choices
        Counter::create(['place_id' => $govt->id, 'service_id' => $govtSvcModels[3]->id, 'name' => 'Window 4', 'location' => 'Ground Floor']);
        Counter::create(['place_id' => $bank->id, 'service_id' => $bankSvcModels[2]->id, 'name' => 'Counter C', 'location' => 'First Floor']);

        // ---------- DEMO CUSTOMERS ----------
        User::create([
            'name' => 'Rahul Verma',
            'email' => 'user@smartqueue.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_USER,
            'phone' => '9111111111',
        ]);

        User::create([
            'name' => 'Sneha Iyer',
            'email' => 'sneha@smartqueue.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_USER,
            'phone' => '9222222222',
        ]);

        // ---------- SETTINGS ----------
        $settings = [
            'business_open' => '09:00',
            'business_close' => '17:00',
            'break_start' => '13:00',
            'break_end' => '14:00',
            'token_prefix' => '',
            'auto_skip_minutes' => '10',
            'daily_token_limit' => '200',
            'show_public_display' => '1',
            'notify_before_turn' => '2',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        $this->call(QueueTokenSeeder::class);
    }
}
