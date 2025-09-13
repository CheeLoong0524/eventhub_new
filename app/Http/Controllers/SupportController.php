<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\SupportInquiry;
use App\Models\Faq;

class SupportController extends Controller
{
    /**
     * Show the customer support main page
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get user-specific support data
        $userSupportData = $this->getUserSupportData($user);
        
        return view('support.index', compact('userSupportData', 'user'));
    }

    /**
     * Show the FAQ page
     */
    public function faq()
    {
        $user = auth()->user();
        
        // Get user-specific FAQs based on their role and account
        $faqs = $this->getUserSpecificFAQs($user);

        return view('support.faq', compact('faqs', 'user'));
    }

    /**
     * Handle contact form submission
     */
    public function contact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Generate unique inquiry ID
            $inquiryId = SupportInquiry::generateInquiryId();
            
            // Create inquiry record in database
            $inquiry = SupportInquiry::create([
                'inquiry_id' => $inquiryId,
                'user_id' => auth()->id(), // Will be null for guest users
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 'pending',
            ]);

            // Log the contact request for backup
            \Log::info('Customer Support Contact Request', [
                'inquiry_id' => $inquiryId,
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'timestamp' => now()
            ]);

            // You can uncomment and configure this to actually send emails
            /*
            Mail::send('emails.support.contact', [
                'inquiry_id' => $inquiryId,
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'category' => $request->category,
                'message' => $request->message
            ], function ($message) use ($request, $inquiryId) {
                $message->to('support@eventhub.com')
                    ->subject('Customer Support: ' . $request->subject . ' [ID: ' . $inquiryId . ']')
                    ->replyTo($request->email, $request->name);
            });
            */

            return redirect()->back()->with('success', 'Thank you for contacting us! Your inquiry ID is: ' . $inquiryId . '. We will get back to you within 24 hours.');

        } catch (\Exception $e) {
            \Log::error('Failed to process support contact request', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Sorry, there was an error sending your message. Please try again later.')
                ->withInput();
        }
    }

    /**
     * Get user-specific support data
     */
    private function getUserSupportData($user)
    {
        if (!$user) {
            return [
                'welcome_message' => 'Welcome to EventHub Support!',
                'user_type' => 'guest',
                'recent_activities' => [],
                'support_priority' => 'normal',
                'personalized_tips' => [
                    'Create an account to get personalized support',
                    'Browse our FAQ for quick answers',
                    'Contact us for immediate assistance'
                ]
            ];
        }

        $data = [
            'welcome_message' => "Welcome back, {$user->name}!",
            'user_type' => $user->role,
            'support_priority' => $this->getUserSupportPriority($user),
            'recent_activities' => $this->getUserRecentActivities($user),
            'personalized_tips' => $this->getPersonalizedTips($user),
            'account_status' => $this->getAccountStatus($user)
        ];

        // Add role-specific data
        switch ($user->role) {
            case 'admin':
                $data['admin_specific'] = $this->getAdminSupportData($user);
                break;
            case 'vendor':
                $data['vendor_specific'] = $this->getVendorSupportData($user);
                break;
            case 'customer':
                $data['customer_specific'] = $this->getCustomerSupportData($user);
                break;
        }

        return $data;
    }

    /**
     * Get user-specific FAQs from database
     */
    private function getUserSpecificFAQs($user)
    {
        // Base categories that everyone can see
        $categories = ['general', 'technical', 'billing', 'event'];
        
        if (!$user) {
            // Guest users only see general and technical FAQs
            $categories = ['general', 'technical'];
        } else {
            // Add role-specific category
            switch ($user->role) {
                case 'admin':
                    $categories[] = 'admin';
                    break;
                case 'vendor':
                    $categories[] = 'vendor';
                    break;
                case 'customer':
                    $categories[] = 'customer';
                    break;
            }
        }

        // Get FAQs from database
        $faqs = Faq::active()
            ->byCategories($categories)
            ->ordered()
            ->get()
            ->map(function ($faq) {
                return [
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category' => $faq->category,
                    'category_label' => $faq->category_label,
                    'category_badge_color' => $faq->category_badge_color,
                ];
            })
            ->toArray();

        return $faqs;
    }

    /**
     * Get user support priority
     */
    private function getUserSupportPriority($user)
    {
        if ($user->role === 'admin') {
            return 'high';
        }
        
        if ($user->role === 'vendor' && $user->vendor) {
            return $user->vendor->is_verified ? 'high' : 'normal';
        }
        
        return 'normal';
    }

    /**
     * Get user recent activities
     */
    private function getUserRecentActivities($user)
    {
        $activities = [];
        
        if ($user->role === 'vendor' && $user->vendor) {
            $activities[] = [
                'type' => 'vendor_application',
                'message' => 'Vendor application status: ' . ucfirst($user->vendor->status),
                'date' => $user->vendor->updated_at
            ];
        }
        
        $activities[] = [
            'type' => 'account_created',
            'message' => 'Account created on ' . $user->created_at->format('M d, Y'),
            'date' => $user->created_at
        ];
        
        return $activities;
    }

    /**
     * Get personalized tips based on user
     */
    private function getPersonalizedTips($user)
    {
        $tips = [];
        
        switch ($user->role) {
            case 'admin':
                $tips = [
                    'Use the admin dashboard to manage all users and events',
                    'Review vendor applications regularly',
                    'Monitor event financials and reports'
                ];
                break;
            case 'vendor':
                $vendor = $user->vendor;
                if ($vendor) {
                    if ($vendor->status === 'pending') {
                        $tips[] = 'Your vendor application is under review';
                    } elseif ($vendor->status === 'approved') {
                        $tips[] = 'Browse available events to apply for booth spaces';
                        $tips[] = 'Keep your vendor profile updated';
                    }
                } else {
                    $tips[] = 'Complete your vendor application to access vendor features';
                }
                break;
            case 'customer':
                $tips = [
                    'Explore events and activities',
                    'Create an account to get personalized recommendations',
                    'Contact event organizers for more information'
                ];
                break;
        }
        
        return $tips;
    }

    /**
     * Get account status information
     */
    private function getAccountStatus($user)
    {
        return [
            'is_active' => $user->is_active,
            'email_verified' => !is_null($user->email_verified_at),
            'auth_method' => $user->auth_method,
            'last_login' => $user->updated_at
        ];
    }

    /**
     * Get admin-specific support data
     */
    private function getAdminSupportData($user)
    {
        return [
            'total_users' => \App\Models\User::count(),
            'pending_vendors' => \App\Models\Vendor::where('status', 'pending')->count(),
            'active_events' => \App\Models\Event::where('status', 'active')->count()
        ];
    }

    /**
     * Get vendor-specific support data
     */
    private function getVendorSupportData($user)
    {
        $vendor = $user->vendor;
        if (!$vendor) {
            return ['has_vendor_profile' => false];
        }
        
        return [
            'has_vendor_profile' => true,
            'vendor_status' => $vendor->status,
            'total_applications' => $vendor->eventApplications()->count(),
            'approved_applications' => $vendor->eventApplications()->where('status', 'approved')->count()
        ];
    }

    /**
     * Get customer-specific support data
     */
    private function getCustomerSupportData($user)
    {
        return [
            'member_since' => $user->created_at->format('M Y'),
            'account_type' => 'Standard Customer'
        ];
    }


    /**
     * Show the check inquiry page
     */
    public function checkInquiry()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('support.index')->with('error', 'Please log in to check your inquiries.');
        }

        // Get user's inquiries
        $inquiries = SupportInquiry::where('user_id', $user->id)
            ->orWhere('email', $user->email) // Also show inquiries submitted with same email
            ->orderBy('created_at', 'desc')
            ->get();

        return view('support.check', compact('inquiries', 'user'));
    }

    /**
     * Show specific inquiry details
     */
    public function showInquiry($inquiryId)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('support.index')->with('error', 'Please log in to view inquiry details.');
        }

        // Find inquiry by inquiry_id
        $inquiry = SupportInquiry::where('inquiry_id', $inquiryId)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('email', $user->email);
            })
            ->first();

        if (!$inquiry) {
            return redirect()->route('support.check')->with('error', 'Inquiry not found or you do not have permission to view it.');
        }

        return view('support.inquiry-details', compact('inquiry', 'user'));
    }
}
