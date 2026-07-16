<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $this->processInventoryDeduction($order);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Only run if status was just changed to confirmed or completed
        if ($order->wasChanged('status')) {
            $this->processInventoryDeduction($order);
        }
    }

    /**
     * Deduct inventory based on the order's items and their BOM.
     */
    private function processInventoryDeduction(Order $order): void
    {
        if ($order->status === Order::STATUS_CANCELLED) {
            // If cancelled and already deducted, restock it
            if ($order->is_inventory_deducted) {
                $this->restockInventory($order);
            }
            return;
        }

        // Only deduct if order is confirmed/preparing/ready/completed
        // Avoid deducting twice
        if ($order->is_inventory_deducted) {
            return;
        }

        if (! in_array($order->status, [Order::STATUS_CONFIRMED, Order::STATUS_PREPARING, Order::STATUS_READY, Order::STATUS_COMPLETED])) {
            return;
        }

        try {
            DB::beginTransaction();

            // Lock order to prevent concurrent updates
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($lockedOrder->is_inventory_deducted) {
                DB::rollBack();
                return;
            }

            // Get order items and their associated menu's master recipes and ingredients
            $order->loadMissing('items.menu.masterRecipes.ingredients');

            $ingredientDeductions = [];

            foreach ($order->items as $orderItem) {
                if (! $orderItem->menu) continue;

                $quantityOrdered = $orderItem->quantity;

                foreach ($orderItem->menu->masterRecipes as $masterRecipe) {
                    $multiplier = $masterRecipe->pivot->multiplier;

                    foreach ($masterRecipe->ingredients as $ingredient) {
                        $takaranPerBiji = $ingredient->pivot->quantity;
                        
                        $totalNeeded = $multiplier * $takaranPerBiji * $quantityOrdered;

                        $ingredientId = $ingredient->id;
                        if (! isset($ingredientDeductions[$ingredientId])) {
                            $ingredientDeductions[$ingredientId] = 0;
                        }
                        $ingredientDeductions[$ingredientId] += $totalNeeded;
                    }
                }
            }

            // Deduct from ingredients
            foreach ($ingredientDeductions as $ingredientId => $amountToDeduct) {
                Ingredient::where('id', $ingredientId)->decrement('current_stock', $amountToDeduct);
            }

            // Mark as deducted
            $lockedOrder->is_inventory_deducted = true;
            $lockedOrder->saveQuietly(); // Use saveQuietly to prevent infinite loop of updated event

            DB::commit();
            Log::info("Inventory deducted for Order #{$order->order_number}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to deduct inventory for Order #{$order->order_number}: " . $e->getMessage());
        }
    }

    /**
     * Restore inventory back when order is cancelled.
     */
    private function restockInventory(Order $order): void
    {
        try {
            DB::beginTransaction();

            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if (! $lockedOrder->is_inventory_deducted) {
                DB::rollBack();
                return;
            }

            $order->loadMissing('items.menu.masterRecipes.ingredients');

            $ingredientRestocks = [];

            foreach ($order->items as $orderItem) {
                if (! $orderItem->menu) continue;

                $quantityOrdered = $orderItem->quantity;

                foreach ($orderItem->menu->masterRecipes as $masterRecipe) {
                    $multiplier = $masterRecipe->pivot->multiplier;

                    foreach ($masterRecipe->ingredients as $ingredient) {
                        $takaranPerBiji = $ingredient->pivot->quantity;
                        
                        $totalNeeded = $multiplier * $takaranPerBiji * $quantityOrdered;

                        $ingredientId = $ingredient->id;
                        if (! isset($ingredientRestocks[$ingredientId])) {
                            $ingredientRestocks[$ingredientId] = 0;
                        }
                        $ingredientRestocks[$ingredientId] += $totalNeeded;
                    }
                }
            }

            // Add back to ingredients
            foreach ($ingredientRestocks as $ingredientId => $amountToAdd) {
                Ingredient::where('id', $ingredientId)->increment('current_stock', $amountToAdd);
            }

            $lockedOrder->is_inventory_deducted = false;
            $lockedOrder->saveQuietly();

            DB::commit();
            Log::info("Inventory restocked for cancelled Order #{$order->order_number}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to restock inventory for Order #{$order->order_number}: " . $e->getMessage());
        }
    }
}
