<?php

/**
 * Vehicle type groupings for frontend routing.
 *
 * Maps URL slugs to body_types.name values (DB exact match — case-sensitive).
 * All body types from BodyTypeSeeder are covered here.
 *
 * File: config/vehicle_types.php
 */
return [

    // ── Passenger Vehicles ────────────────────────────────────────────────────

    'cars' => [
        'label'         => 'Cars',
        'heading_label' => 'cars',
        'body_types'    => ['Sedan', 'Coupe', 'Convertible', 'Wagon', 'Hatchback', 'Classic'],
        'title'         => 'Used Cars for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Browse quality used cars at Angel Motors Inc in Smyrna, TN. Serving Nashville, Murfreesboro & surrounding areas.',
        'keywords'      => 'used cars smyrna tn, used cars nashville, pre-owned cars smyrna',
    ],

    'suvs' => [
        'label'         => 'SUVs',
        'heading_label' => 'SUVs',
        'body_types'    => ['SUV'],
        'title'         => 'Used SUVs for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Shop reliable used SUVs at Angel Motors Inc in Smyrna, TN. Serving Nashville, Murfreesboro & surrounding areas.',
        'keywords'      => 'used suvs smyrna tn, used suv nashville, pre-owned suv smyrna',
    ],

    'trucks' => [
        'label'         => 'Trucks',
        'heading_label' => 'trucks',
        'body_types'    => ['Pickup truck', 'Pickup utility'],
        'title'         => 'Used Trucks for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Find dependable used trucks at Angel Motors Inc in Smyrna, TN. Serving Nashville, Murfreesboro & surrounding areas.',
        'keywords'      => 'used trucks smyrna tn, pickup trucks nashville, pre-owned trucks smyrna',
    ],

    'vans' => [
        'label'         => 'Vans',
        'heading_label' => 'vans',
        'body_types'    => [
            'Van', 'Minivan', 'Passenger van', 'Cargo van',
            'Sprinter Van', 'Commercial van', 'Crew van', 'Cutaway van',
        ],
        'title'         => 'Used Vans for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Browse used vans at Angel Motors Inc in Smyrna, TN. Serving Nashville, Murfreesboro & surrounding areas.',
        'keywords'      => 'used vans smyrna tn, cargo vans nashville, pre-owned vans smyrna',
    ],

    'convertibles' => [
        'label'         => 'Convertibles',
        'heading_label' => 'convertibles',
        'body_types'    => ['Convertible'],
        'title'         => 'Used Convertibles for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Find used convertibles at Angel Motors Inc in Smyrna, TN. Serving Nashville, Murfreesboro & surrounding areas.',
        'keywords'      => 'used convertibles smyrna tn, convertibles nashville',
    ],

    'hatchbacks' => [
        'label'         => 'Hatchbacks',
        'heading_label' => 'hatchbacks',
        'body_types'    => ['Hatchback'],
        'title'         => 'Used Hatchbacks for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Shop used hatchbacks at Angel Motors Inc in Smyrna, TN. Serving Nashville, Murfreesboro & surrounding areas.',
        'keywords'      => 'used hatchbacks smyrna tn, hatchbacks nashville',
    ],

    'classics' => [
        'label'         => 'Classics',
        'heading_label' => 'classics',
        'body_types'    => ['Classic'],
        'title'         => 'Used Classic Cars for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Find used classic cars at Angel Motors Inc in Smyrna, TN. Serving Nashville, Murfreesboro & surrounding areas.',
        'keywords'      => 'used classic cars smyrna tn, classic cars nashville',
    ],

    // ── Commercial Vehicles ───────────────────────────────────────────────────

    'commercial-trucks' => [
        'label'         => 'Commercial Trucks',
        'heading_label' => 'commercial trucks',
        'body_types'    => [
            'Box truck', 'Boom truck', 'Bucket truck', 'Cab and chassis',
            'Cabover truck', 'Contractor truck', 'Crane truck', 'Curtain Side truck',
            'Day cab', 'Dump truck', 'Dump truck body', 'Enclosed service truck',
            'Flatbed truck', 'Flatbed truck body', 'Grapple truck', 'Hooklift truck',
            'Landscape truck', 'Mechanics truck', 'Moving truck', 'Refrigerated truck',
            'Refrigerated truck body', 'Roll off truck', 'Rollback truck', 'Rotator',
            'Scissor lift truck', 'Service body truck', 'Service truck', 'Sleeper cab',
            'Step van', 'Tow truck', 'Tractor truck', 'Truck body',
            'Utility/service truck', 'Winch', 'Wrecker',
            // Commercial Trucks group
            'Cab chassis', 'Compact Track Loaders', 'Concrete Equipment', 'Excavators',
            'HVAC', 'KUV', 'Light Vehicles', 'Mini Dumpers & Loaders', 'Roll Off',
            'Scissor lift', 'Scissor Lifts', 'Skid-Steer Loaders', 'Sleeper',
            'Specialty vehicle', 'Towable Boom Lifts', 'Tractor', 'Tractors',
            'Wheel Loaders',
        ],
        'title'         => 'Used Commercial Trucks for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Browse used commercial trucks at Angel Motors Inc in Smyrna, TN.',
        'keywords'      => 'used commercial trucks smyrna tn, work trucks nashville',
    ],

    'buses' => [
        'label'         => 'Buses',
        'heading_label' => 'buses',
        'body_types'    => [
            'Bus', 'Activity Bus', 'Ada Bus', 'Commercial Bus',
            'Executive Coach', 'Mini School Bus', 'School Bus', 'Shuttle bus',
        ],
        'title'         => 'Used Buses for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Browse used buses at Angel Motors Inc in Smyrna, TN.',
        'keywords'      => 'used buses smyrna tn, school buses nashville',
    ],

    'utility-vehicles' => [
        'label'         => 'Utility Vehicles',
        'heading_label' => 'utility vehicles',
        'body_types'    => [
            'Utility vehicle', 'Wheelchair Accessible Vehicle',
            'Lawn mower', 'Liftgate', 'Refrigerated Cargo van',
            'Vending trailer', 'Commercial',
        ],
        'title'         => 'Used Utility Vehicles for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Browse used utility vehicles at Angel Motors Inc in Smyrna, TN.',
        'keywords'      => 'used utility vehicles smyrna tn, work vehicles nashville',
    ],

    // ── Powersports ───────────────────────────────────────────────────────────

    'powersports' => [
        'label'         => 'Powersports',
        'heading_label' => 'powersports',
        'body_types'    => ['ATV', 'Motorcycle', 'MPV', 'Powersports', 'Scooter', 'Snowmobile', 'Watercraft'],
        'title'         => 'Used Powersports for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Browse used powersports vehicles at Angel Motors Inc in Smyrna, TN.',
        'keywords'      => 'used motorcycles smyrna tn, used atv nashville, powersports smyrna',
    ],

    // ── Recreational Vehicles ─────────────────────────────────────────────────

    'rvs' => [
        'label'         => 'RVs',
        'heading_label' => 'RVs',
        'body_types'    => [
            'Class A', 'Class B', 'Class C', 'Super C',
            'Motor Home Class A', 'Motor Home Class A - Diesel',
            'Motor Home Class B', 'Motor Home Class B - Diesel',
            'Motor Home Class C',
        ],
        'title'         => 'Used RVs for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Browse used RVs and motorhomes at Angel Motors Inc in Smyrna, TN.',
        'keywords'      => 'used rvs smyrna tn, used motorhomes nashville',
    ],

    'trailers' => [
        'label'         => 'Trailers',
        'heading_label' => 'trailers',
        'body_types'    => [
            'Travel Trailer', 'Fifth Wheel', 'Destination Trailer', 'Expandables',
            'Pop-Up', 'Teardrop Trailers', 'Toy Hauler',
            // Trailers group
            'BBQ trailer', 'Camper', 'Cargo trailer', 'Concession trailer',
            'Dump trailer', 'Dump Trailers', 'Enclosed trailer', 'Equipment trailer',
            'Folding Pop-Up Camper', 'Pop Up', 'Race trailer', 'Refrigerated trailer',
            'Roll off trailer', 'Tag-Along Trailers', 'Teardrop Trailer', 'Towable RV',
            'Toy Hauler Expandable', 'Toy Hauler Fifth Wheel', 'Trailer', 'Vending trailer',
        ],
        'title'         => 'Used Trailers for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Browse used trailers at Angel Motors Inc in Smyrna, TN.',
        'keywords'      => 'used trailers smyrna tn, travel trailers nashville',
    ],

    // ── Other ─────────────────────────────────────────────────────────────────

    'other' => [
        'label'         => 'Other',
        'heading_label' => 'other vehicles',
        'body_types'    => [
            'Aircraft', 'Attachment/Implement', 'Auger Attachments',
            'Generator', 'Generators', 'Golf cart', 'Lawn & Landscape',
            'Light Compaction', 'Loader Accessories', 'Loader Attachments',
            'Parts for sale', 'Pumps & Hoses', 'Tractor Attachments', 'Unknown',
        ],
        'title'         => 'Other Vehicles for Sale in Smyrna, TN | Angel Motors Inc',
        'description'   => 'Browse other vehicles at Angel Motors Inc in Smyrna, TN.',
        'keywords'      => 'other vehicles smyrna tn, specialty vehicles nashville',
    ],

];
