<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\User;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a vendor user for events
        $vendor = User::where('role', 'vendor')->first();
        if (!$vendor) {
            $vendor = User::create([
                'name' => 'Event Organizer',
                'email' => 'vendor@example.com',
                'password' => bcrypt('password'),
                'role' => 'vendor',
                'is_active' => true,
                'auth_method' => 'laravel'
            ]);
        }

        $events = [
            [
                'name' => 'Malaysia Tech Summit 2024',
                'description' => 'Join us for the biggest tech conference in Malaysia featuring the latest innovations in AI, blockchain, and cloud computing. Network with industry leaders and discover cutting-edge technologies in the heart of Kuala Lumpur.',
                'date' => Carbon::now()->addDays(30),
                'time' => '09:00:00',
                'venue' => 'Kuala Lumpur Convention Centre',
                'location' => 'Kuala Lumpur, Malaysia',
                'category' => 'Technology',
                'image_url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
                'status' => 'published',
                'max_attendees' => 500,
                'is_featured' => true,
                'popularity_score' => 95,
                'ticket_types' => [
                    ['name' => 'Early Bird', 'description' => 'Limited time offer', 'price' => 199.00, 'available_quantity' => 100, 'max_per_order' => 4],
                    ['name' => 'Regular', 'description' => 'Standard admission', 'price' => 299.00, 'available_quantity' => 300, 'max_per_order' => 6],
                    ['name' => 'VIP', 'description' => 'Premium experience with networking dinner', 'price' => 599.00, 'available_quantity' => 50, 'max_per_order' => 2],
                ]
            ],
            [
                'name' => 'Jazz Night at The Majestic Hotel',
                'description' => 'Experience an intimate evening of smooth jazz featuring world-renowned musicians in the elegant setting of The Majestic Hotel. Perfect for a romantic date or a relaxing night out in Kuala Lumpur.',
                'date' => Carbon::now()->addDays(15),
                'time' => '20:00:00',
                'venue' => 'The Majestic Hotel',
                'location' => 'Kuala Lumpur, Malaysia',
                'category' => 'Music',
                'image_url' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800',
                'status' => 'published',
                'max_attendees' => 200,
                'is_featured' => false,
                'popularity_score' => 78,
                'ticket_types' => [
                    ['name' => 'General Admission', 'description' => 'Standing room', 'price' => 90.00, 'available_quantity' => 150, 'max_per_order' => 4],
                    ['name' => 'Table Seating', 'description' => 'Reserved table with bottle service', 'price' => 240.00, 'available_quantity' => 25, 'max_per_order' => 6],
                ]
            ],
            [
                'name' => 'Malaysian Food & Culture Festival',
                'description' => 'Indulge in the finest Malaysian cuisine and traditional performances. Meet celebrity chefs and discover authentic flavors from all 13 states of Malaysia.',
                'date' => Carbon::now()->addDays(45),
                'time' => '12:00:00',
                'venue' => 'Dataran Merdeka',
                'location' => 'Kuala Lumpur, Malaysia',
                'category' => 'Food & Drink',
                'image_url' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800',
                'status' => 'published',
                'max_attendees' => 1000,
                'is_featured' => true,
                'popularity_score' => 88,
                'ticket_types' => [
                    ['name' => 'Tasting Pass', 'description' => 'Access to all tasting booths', 'price' => 150.00, 'available_quantity' => 500, 'max_per_order' => 8],
                    ['name' => 'VIP Experience', 'description' => 'Exclusive chef meet & greet + premium tastings', 'price' => 300.00, 'available_quantity' => 100, 'max_per_order' => 4],
                    ['name' => 'Student', 'description' => 'Valid student ID required', 'price' => 70.00, 'available_quantity' => 200, 'max_per_order' => 2],
                ]
            ],
            [
                'name' => 'Penang Marathon Training Workshop',
                'description' => 'Get expert training tips and nutrition advice from professional runners along the beautiful beaches of Penang. Perfect for beginners and experienced runners alike.',
                'date' => Carbon::now()->addDays(20),
                'time' => '08:00:00',
                'venue' => 'Batu Ferringhi Beach',
                'location' => 'Penang, Malaysia',
                'category' => 'Sports & Fitness',
                'image_url' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800',
                'status' => 'published',
                'max_attendees' => 100,
                'is_featured' => false,
                'popularity_score' => 65,
                'ticket_types' => [
                    ['name' => 'Workshop Only', 'description' => 'Training session and materials', 'price' => 50.00, 'available_quantity' => 80, 'max_per_order' => 4],
                    ['name' => 'Complete Package', 'description' => 'Workshop + nutrition guide + follow-up session', 'price' => 90.00, 'available_quantity' => 20, 'max_per_order' => 2],
                ]
            ],
            [
                'name' => 'Melaka Art Gallery Opening',
                'description' => 'Celebrate the opening of our new contemporary art exhibition featuring works from emerging and established Malaysian artists in the historic city of Melaka.',
                'date' => Carbon::now()->addDays(10),
                'time' => '18:00:00',
                'venue' => 'Melaka Art Gallery',
                'location' => 'Melaka, Malaysia',
                'category' => 'Arts & Culture',
                'image_url' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=800',
                'status' => 'published',
                'max_attendees' => 150,
                'is_featured' => false,
                'popularity_score' => 72,
                'ticket_types' => [
                    ['name' => 'General Admission', 'description' => 'Gallery access and refreshments', 'price' => 40.00, 'available_quantity' => 100, 'max_per_order' => 6],
                    ['name' => 'Artist Meet & Greet', 'description' => 'Private session with featured artists', 'price' => 100.00, 'available_quantity' => 30, 'max_per_order' => 2],
                ]
            ],
            [
                'name' => 'Malaysian Startup Pitch Competition',
                'description' => 'Watch innovative Malaysian startups pitch their ideas to a panel of investors. Great networking opportunity for entrepreneurs in the growing Malaysian tech scene.',
                'date' => Carbon::now()->addDays(25),
                'time' => '14:00:00',
                'venue' => 'KLCC Convention Centre',
                'location' => 'Kuala Lumpur, Malaysia',
                'category' => 'Business',
                'image_url' => 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=800',
                'status' => 'published',
                'max_attendees' => 300,
                'is_featured' => true,
                'popularity_score' => 82,
                'ticket_types' => [
                    ['name' => 'Observer', 'description' => 'Watch the pitches', 'price' => 60.00, 'available_quantity' => 200, 'max_per_order' => 4],
                    ['name' => 'Networking Pass', 'description' => 'Includes networking session with investors', 'price' => 150.00, 'available_quantity' => 80, 'max_per_order' => 2],
                    ['name' => 'Investor', 'description' => 'VIP access and judging panel', 'price' => 400.00, 'available_quantity' => 20, 'max_per_order' => 1],
                ]
            ],
            [
                'name' => 'Port Dickson Wellness Retreat',
                'description' => 'Rejuvenate your mind and body with a weekend of yoga, meditation, and wellness activities at the beautiful beaches of Port Dickson.',
                'date' => Carbon::now()->addDays(60),
                'time' => '07:00:00',
                'venue' => 'Lexis Hibiscus Port Dickson',
                'location' => 'Port Dickson, Malaysia',
                'category' => 'Wellness',
                'image_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800',
                'status' => 'published',
                'max_attendees' => 50,
                'is_featured' => false,
                'popularity_score' => 70,
                'ticket_types' => [
                    ['name' => 'Weekend Pass', 'description' => 'All activities and meals included', 'price' => 598.00, 'available_quantity' => 40, 'max_per_order' => 2],
                    ['name' => 'Day Pass', 'description' => 'Single day access', 'price' => 300.00, 'available_quantity' => 10, 'max_per_order' => 4],
                ]
            ],
            [
                'name' => 'Comedy Night at Zouk KL',
                'description' => 'Laugh the night away with top Malaysian comedians and international acts. Perfect for a fun night out with friends in the heart of Kuala Lumpur.',
                'date' => Carbon::now()->addDays(7),
                'time' => '21:00:00',
                'venue' => 'Zouk Kuala Lumpur',
                'location' => 'Kuala Lumpur, Malaysia',
                'category' => 'Entertainment',
                'image_url' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=800',
                'status' => 'published',
                'max_attendees' => 120,
                'is_featured' => false,
                'popularity_score' => 85,
                'ticket_types' => [
                    ['name' => 'General Admission', 'description' => 'Standing room', 'price' => 50.00, 'available_quantity' => 80, 'max_per_order' => 6],
                    ['name' => 'VIP Seating', 'description' => 'Reserved seats with drink service', 'price' => 100.00, 'available_quantity' => 40, 'max_per_order' => 4],
                ]
            ]
        ];

        foreach ($events as $eventData) {
            $ticketTypes = $eventData['ticket_types'];
            unset($eventData['ticket_types']);
            
            $eventData['created_by'] = $vendor->id;
            $event = Event::create($eventData);

            foreach ($ticketTypes as $ticketTypeData) {
                $ticketTypeData['event_id'] = $event->id;
                $ticketTypeData['sold_quantity'] = rand(0, $ticketTypeData['available_quantity'] / 4);
                $ticketTypeData['is_active'] = true;
                $ticketTypeData['sale_start_date'] = Carbon::now()->subDays(rand(1, 30));
                $ticketTypeData['sale_end_date'] = Carbon::now()->addDays(rand(30, 90));
                
                TicketType::create($ticketTypeData);
            }
        }
    }
}
