<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\VendorEventApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show financial reports dashboard
     */
    public function index()
    {
        // Get overall statistics
        $totalEvents = Event::count();
        $activeEvents = Event::where('status', 'active')->count();
        $completedEvents = Event::where('status', 'completed')->count();
        
        // Financial statistics
        $totalRevenue = Event::sum('total_revenue');
        $totalCosts = Event::sum('total_costs');
        $netProfit = Event::sum('net_profit');
        
        // Booth statistics
        $totalBooths = Event::sum('booth_quantity');
        $soldBooths = Event::sum('booth_sold');
        $boothRevenue = VendorEventApplication::where('status', 'paid')->sum('final_amount');
        
        // Recent events with financial data
        $recentEvents = Event::with(['venue'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Top performing events by revenue
        $topEvents = Event::orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();
        
        // Monthly revenue data for chart
        $monthlyRevenue = Event::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_revenue) as revenue'),
                DB::raw('SUM(total_costs) as costs'),
                DB::raw('SUM(net_profit) as profit')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return view('admin.reports.index', compact(
            'totalEvents', 'activeEvents', 'completedEvents',
            'totalRevenue', 'totalCosts', 'netProfit',
            'totalBooths', 'soldBooths', 'boothRevenue',
            'recentEvents', 'topEvents', 'monthlyRevenue'
        ));
    }

    /**
     * Show detailed event financial report
     */
    public function event($id)
    {
        $event = Event::with(['venue', 'vendorApplications.vendor'])
            ->findOrFail($id);
        
        // Get booth applications for this event
        $boothApplications = VendorEventApplication::with(['vendor'])
            ->where('event_id', $id)
            ->get();
        
        // Calculate financial breakdown
        $boothRevenue = $event->calculateBoothRevenue();
        $ticketRevenue = $event->calculateTicketRevenue();
        $totalRevenue = $event->calculateTotalRevenue();
        $totalCosts = $event->calculateTotalCosts();
        $netProfit = $event->calculateNetProfit();
        
        // Booth occupancy rate
        $occupancyRate = $event->booth_quantity > 0 ? 
            ($event->booth_sold / $event->booth_quantity) * 100 : 0;
        
        // Calculate detailed booth sales breakdown
        $boothSalesBreakdown = $this->calculateBoothSalesBreakdown($boothApplications);
        
        return view('admin.reports.event', compact(
            'event', 'boothApplications', 'boothRevenue', 
            'ticketRevenue', 'totalRevenue', 'totalCosts', 
            'netProfit', 'occupancyRate', 'boothSalesBreakdown'
        ));
    }
    
    /**
     * Calculate detailed booth sales breakdown by size
     */
    private function calculateBoothSalesBreakdown($boothApplications)
    {
        $breakdown = [];
        $totalBooths = 0;
        $totalRevenue = 0;
        
        foreach ($boothApplications as $application) {
            if ($application->status === 'paid') {
                $boothSize = $application->booth_size ?? 'Standard';
                $quantity = $application->booth_quantity ?? 1;
                $revenue = $application->final_amount ?? 0;
                
                if (!isset($breakdown[$boothSize])) {
                    $breakdown[$boothSize] = [
                        'size' => $boothSize,
                        'quantity' => 0,
                        'revenue' => 0,
                        'average_price' => 0
                    ];
                }
                
                $breakdown[$boothSize]['quantity'] += $quantity;
                $breakdown[$boothSize]['revenue'] += $revenue;
                $totalBooths += $quantity;
                $totalRevenue += $revenue;
            }
        }
        
        // Calculate average prices
        foreach ($breakdown as $size => $data) {
            if ($data['quantity'] > 0) {
                $breakdown[$size]['average_price'] = $data['revenue'] / $data['quantity'];
            }
        }
        
        return [
            'breakdown' => $breakdown,
            'total_booths' => $totalBooths,
            'total_revenue' => $totalRevenue
        ];
    }

}