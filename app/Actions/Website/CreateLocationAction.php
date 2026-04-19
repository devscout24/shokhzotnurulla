<?php

namespace App\Actions\Website;

use App\Helpers\TimeHelper;
use App\Models\Dealership\Dealer;
use App\Models\Website\Location;
use App\Models\Website\LocationEmail;
use App\Models\Website\LocationHour;
use App\Models\Website\LocationPhone;
use App\Models\Website\LocationSpecialHour;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CreateLocationAction
{
    public function __invoke(Dealer $dealer, array $data): Location
    {
        return DB::transaction(function () use ($dealer, $data) {
            // Create location
            $location = $dealer->locations()->create([
                'name' => $data['name'],
                'street1' => $data['street1'],
                'street2' => $data['street2'] ?? null,
                'city' => $data['city'],
                'state' => $data['state'],
                'postalcode' => $data['postalcode'],
                'country' => $data['country'],
                'map_override' => $data['map_override'] ?? null,
                'order' => $dealer->locations()->max('order') + 1,
            ]);

            // Save phones
            foreach (['main', 'sales', 'service', 'parts', 'rentals', 'collision'] as $type) {
                $phone = $data['phone_' . $type] ?? null;
                if ($phone) {
                    $location->phones()->create([
                        'type' => $type,
                        'number' => $phone,
                    ]);
                }
            }

            // Save emails
            foreach (['main', 'sales', 'service', 'parts', 'rentals', 'collision'] as $type) {
                $email = $data['email_' . $type] ?? null;
                if ($email) {
                    $location->emails()->create([
                        'type' => $type,
                        'email' => $email,
                    ]);
                }
            }

            // Save regular hours
            $departments = ['sales', 'service', 'parts', 'rentals', 'collision'];
            foreach ($departments as $department) {
                $hoursData = $data['hours'][$department] ?? [];

                foreach ($hoursData as $dayIndex => $hour) {
                    $open = isset($hour['open']) ? TimeHelper::toDatabase($hour['open']) : null;
                    $close = isset($hour['close']) ? TimeHelper::toDatabase($hour['close']) : null;

                    $location->hours()->create([
                        'department' => $department,
                        'day_of_week' => $dayIndex + 1,
                        'open_time' => $open,
                        'close_time' => $close,
                        'is_closed' => $hour['is_closed'] ?? false,
                        'appointment_only' => $hour['appointment_only'] ?? false,
                    ]);
                }
            }

            // Save special hours
            foreach ($data['special_hours'] ?? [] as $special) {
                $location->specialHours()->create([
                    'department' => $special['department'] ?? null,
                    'date' => $special['date'],
                    'open_time' => $special['open_time'] ?? null,
                    'close_time' => $special['close_time'] ?? null,
                    'is_closed' => $special['is_closed'] ?? false,
                    'appointment_only' => $special['appointment_only'] ?? false,
                ]);
            }

            return $location;
        });
    }
}