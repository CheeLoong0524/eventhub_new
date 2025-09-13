<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            // General FAQs
            [
                'question' => 'How do I create an account?',
                'answer' => 'Click the "Sign In" button in the top navigation and follow the registration process. You can use Firebase authentication for quick signup.',
                'category' => 'general',
            ],
            [
                'question' => 'What payment methods are accepted?',
                'answer' => 'We accept various payment methods including credit cards, debit cards, and online banking. All payments are processed securely through our payment gateway.',
                'category' => 'general',
            ],
            [
                'question' => 'What if I have technical issues?',
                'answer' => 'If you experience any technical issues, please contact our support team using the contact form below. We aim to respond to all technical inquiries within 24 hours.',
                'category' => 'general',
            ],

            // Admin FAQs
            [
                'question' => 'How do I manage user accounts?',
                'answer' => 'Use the Admin Dashboard to view, edit, and manage all user accounts. You can approve vendors, suspend accounts, and view user statistics.',
                'category' => 'admin',
            ],
            [
                'question' => 'How do I review vendor applications?',
                'answer' => 'Go to Admin > Vendor Management > Applications to review pending vendor applications. You can approve, reject, or request more information.',
                'category' => 'admin',
            ],
            [
                'question' => 'How do I manage events?',
                'answer' => 'Access the Events section in your admin dashboard to create, edit, and manage all events. You can track financials, manage applications, and monitor event progress.',
                'category' => 'admin',
            ],

            // Vendor FAQs
            [
                'question' => 'How can I apply as a vendor?',
                'answer' => 'To apply as a vendor, go to the Vendor section and click "Apply as Vendor". Fill out the comprehensive application form with your business details.',
                'category' => 'vendor',
            ],
            [
                'question' => 'How long does vendor approval take?',
                'answer' => 'Vendor applications are typically reviewed within 3-5 business days. You will receive an email notification once your application has been processed.',
                'category' => 'vendor',
            ],
            [
                'question' => 'How do I apply for event booths?',
                'answer' => 'Browse available events in your vendor dashboard and click "Apply" on events that interest you. Fill out the application form and submit your booth request.',
                'category' => 'vendor',
            ],
            [
                'question' => 'How do I update my vendor profile?',
                'answer' => 'Go to your vendor dashboard and click on "Profile" to update your business information, contact details, and service offerings.',
                'category' => 'vendor',
            ],

            // Customer FAQs
            [
                'question' => 'How do I browse events?',
                'answer' => 'Visit the Events section to see all available events. You can filter by date, location, and event type to find events that interest you.',
                'category' => 'customer',
            ],
            [
                'question' => 'How do I contact event organizers?',
                'answer' => 'You can contact event organizers through the event details page. Each event has contact information for the organizer.',
                'category' => 'customer',
            ],
            [
                'question' => 'How do I purchase event tickets?',
                'answer' => 'Select the event you want to attend, choose your ticket type, and proceed to checkout. You can pay securely using various payment methods.',
                'category' => 'customer',
            ],

            // Technical FAQs
            [
                'question' => 'What browsers are supported?',
                'answer' => 'EventHub works best with modern browsers including Chrome, Firefox, Safari, and Edge. We recommend using the latest version for the best experience.',
                'category' => 'technical',
            ],
            [
                'question' => 'How do I reset my password?',
                'answer' => 'Click on "Forgot Password" on the login page and enter your email address. You will receive instructions to reset your password.',
                'category' => 'technical',
            ],
            [
                'question' => 'Why can\'t I upload my documents?',
                'answer' => 'Make sure your files are in PDF, JPG, or PNG format and under 5MB in size. If you continue to have issues, contact our support team.',
                'category' => 'technical',
            ],

            // Billing FAQs
            [
                'question' => 'How do I view my payment history?',
                'answer' => 'Go to your dashboard and click on "Payment History" to view all your past transactions and receipts.',
                'category' => 'billing',
            ],
            [
                'question' => 'Can I get a refund?',
                'answer' => 'Refunds are available up to 7 days before the event, minus a 10% processing fee. Contact support for refund requests.',
                'category' => 'billing',
            ],
            [
                'question' => 'How do I update my payment method?',
                'answer' => 'Go to your profile settings and click on "Payment Methods" to add, update, or remove your payment information.',
                'category' => 'billing',
            ],

            // Event FAQs
            [
                'question' => 'How do I create an event?',
                'answer' => 'To create an event, you need to be logged in as an admin. Go to the Events section and click "Create New Event". Fill in all the required details including venue, date, time, and pricing information.',
                'category' => 'event',
            ],
            [
                'question' => 'How do I manage event applications?',
                'answer' => 'Access the event management section to view and process vendor applications, manage booth assignments, and track event progress.',
                'category' => 'event',
            ],
            [
                'question' => 'How do I track event financials?',
                'answer' => 'Each event has a financial dashboard showing costs, revenue, and profit calculations. Access this through the event management section.',
                'category' => 'event',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
