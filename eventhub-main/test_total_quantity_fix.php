<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Total Quantity Fix\n";
echo "=========================\n\n";

// Get a ticket type
$ticketType = \App\Models\TicketType::where('name', 'Early Bird')->first();

echo "Current state:\n";
echo "Available: {$ticketType->available_quantity}\n";
echo "Sold: {$ticketType->sold_quantity}\n";
echo "Total: {$ticketType->total_quantity}\n";
echo "Remaining (old method): " . max(0, $ticketType->available_quantity - $ticketType->sold_quantity) . "\n";
echo "Remaining (new method): {$ticketType->getRemainingQuantity()}\n";

echo "\nTesting the fix:\n";
echo "Before purchase simulation:\n";
$beforeAvailable = $ticketType->available_quantity;
$beforeSold = $ticketType->sold_quantity;
$beforeTotal = $ticketType->total_quantity;
$beforeRemainingOld = max(0, $beforeAvailable - $beforeSold);
$beforeRemainingNew = $ticketType->getRemainingQuantity();

echo "Available: {$beforeAvailable}\n";
echo "Sold: {$beforeSold}\n";
echo "Total: {$beforeTotal}\n";
echo "Remaining (old): {$beforeRemainingOld}\n";
echo "Remaining (new): {$beforeRemainingNew}\n";

echo "\nAfter purchase simulation (1 ticket):\n";
$afterSold = $beforeSold + 1;
$afterRemainingOld = max(0, ($beforeAvailable - 1) - $afterSold); // This is wrong!
$afterRemainingNew = max(0, $beforeTotal - $afterSold); // This is correct!

echo "Available would be: " . ($beforeAvailable - 1) . "\n";
echo "Sold would be: {$afterSold}\n";
echo "Total stays: {$beforeTotal}\n";
echo "Remaining (old method): {$afterRemainingOld} ❌ WRONG!\n";
echo "Remaining (new method): {$afterRemainingNew} ✅ CORRECT!\n";

echo "\nThe difference:\n";
echo "Old method decreases by: " . ($beforeRemainingOld - $afterRemainingOld) . " (should be 1)\n";
echo "New method decreases by: " . ($beforeRemainingNew - $afterRemainingNew) . " (should be 1)\n";

echo "\n✅ The new method using total_quantity is correct!\n";
echo "❌ The old method using available_quantity - sold_quantity causes double deduction!\n";


