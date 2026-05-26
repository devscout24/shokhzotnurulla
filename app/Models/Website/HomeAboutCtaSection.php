<?php

namespace App\Models\Website;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeAboutCtaSection extends Model
{
    protected $table = 'home_about_cta_sections';

    protected $fillable = [
        'dealer_id',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public static function defaultContent(): array
    {
        return [
            'about' => [
                'eyebrow' => 'ABOUT US',
                'heading' => 'Find your next quality pre-owned vehicle in Smyrna, TN',
                'paragraphs' => [
                    'At Angel Motors Inc., we make car buying simple, transparent, and stress-free. Serving Smyrna, TN and surrounding areas since 2015, we are committed to helping you drive away with confidence.',
                    'Explore our <a href="/inventory/cars">wide selection of inspected cars</a>, <a href="/inventory/trucks">trucks</a>, and <a href="/inventory/suvs">SUVs</a>. Whether you are looking for a reliable daily driver or something with a little more space and power, our team is here to help you find the right vehicle for your lifestyle and budget.',
                ],
                'image_url' => 'assets/frontend/img/angel-motors-top-rated-dealer-2.webp',
                'image_alt' => 'Angel Motors top rated dealer',
            ],
            'stats' => [
                [
                    'icon' => 'fa-solid fa-car',
                    'title' => 'All makes & models',
                    'text' => 'Curated selection of top automotive brands',
                ],
                [
                    'icon' => 'fa-solid fa-star',
                    'title' => '4.4 stars',
                    'text' => 'On Google Reviews from our local community',
                ],
                [
                    'icon' => 'fa-regular fa-comment',
                    'title' => '200+',
                    'text' => 'Reviews from happy customers',
                ],
                [
                    'icon' => 'fa-solid fa-shield-halved',
                    'title' => 'Hand-selected quality',
                    'text' => 'Every vehicle rigorously inspected for reliability and value',
                ],
            ],
            'card' => [
                'icon' => 'fa-solid fa-shield-halved',
                'title' => 'Driven by transparency and reliability',
                'text' => 'As a Certified CARFAX Advantage Dealer, we provide a free CARFAX Vehicle History Report with every vehicle. From mechanical inspections to final detailing, we make sure each vehicle meets our standards before it reaches you.',
                'image_url' => 'assets/frontend/img/car-inspection.webp',
                'image_alt' => 'Car inspection',
            ],
            'ctas' => [
                [
                    'title' => 'Financing made simple',
                    'text' => 'We offer arranged financing solutions designed to help you secure the approval that fits your situation. Our team works with trusted lenders to make the process smooth, transparent, and straightforward from start to finish.',
                    'link_url' => '/get-approved',
                ],
                [
                    'title' => 'Here for you beyond the sale',
                    'text' => 'Our commitment does not end when you drive off the lot. From scheduling service to answering your questions, our team is here to provide dependable support and guidance you can rely on long after your purchase.',
                    'link_url' => '/schedule-service',
                ],
            ],
        ];
    }
}
