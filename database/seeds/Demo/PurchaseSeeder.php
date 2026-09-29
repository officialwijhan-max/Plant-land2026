<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Account\Entities\BankAccount;
use Modules\Contact\Entities\ContactModel;
use Modules\Inventory\Entities\ShowRoom;
use Modules\Inventory\Entities\StockReport;
use Modules\Product\Entities\ProductHistory;
use Modules\Product\Entities\ProductSku;
use Modules\Purchase\Entities\ProductItemDetail;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Repositories\PurchaseOrderRepositoryInterface;
use Modules\Sale\Entities\Payment;

/**
 * Purchase orders from suppliers over the last ~2 months, restocking the
 * showroom. The header/items/stock movement are written directly against
 * the Eloquent models rather than calling PurchaseOrderRepository::
 * create() - that method expects an HTTP-request-shaped array (composite
 * strings like "warehouse-1", combo product branches, serial numbers,
 * etc.) that isn't a stable contract to target from a seeder.
 *
 * The accounting side (journal/cash/bank vouchers, chart-of-account
 * balances that the dashboard's bank/cash/profit figures read from) is
 * NOT reimplemented here - it's genuinely a double-entry engine and
 * getting it wrong by hand would produce plausible-looking but incorrect
 * numbers, worse than the obviously-empty state before it. Instead this
 * calls the real PurchaseOrderRepository::approve() for each order,
 * reusing the app's own tested posting logic. Requires
 * ChartAccountSeeder to have run first (approve() needs a ledger account
 * per supplier to post against).
 */
class PurchaseSeeder extends Seeder
{
    public function run()
    {
        if (PurchaseOrder::count() > 0) {
            $this->command->info('purchase_orders already has data, skipping.');
            return;
        }

        $adminId = DB::table('users')->where('role_id', 1)->value('id');
        if ($adminId) {
            Auth::loginUsingId($adminId);
        }

        $showroom = ShowRoom::find(1);
        $suppliers = ContactModel::where('contact_type', 'Supplier')->pluck('id');
        $skus = ProductSku::with('product')->get();
        $bankChartAccountId = BankAccount::value('chart_account_id');

        if (! $showroom || $suppliers->isEmpty() || $skus->isEmpty() || ! $bankChartAccountId) {
            $this->command->warn('Missing showroom/suppliers/products/bank account - run ReferenceDataSeeder, ProductCatalogSeeder, ContactSeeder, BankAccountSeeder first.');
            return;
        }

        $orderCount = 15;
        $invoiceSeq = 1;
        $approved = 0;

        for ($i = 0; $i < $orderCount; $i++) {
            $date = now()->subDays(rand(2, 60));
            $lineCount = rand(3, 6);
            $lines = $skus->random(min($lineCount, $skus->count()));

            $items = [];
            $amount = 0;
            $totalQuantity = 0;

            foreach ($lines as $sku) {
                $qty = rand(10, 60);
                $price = $sku->purchase_price;
                $subTotal = $price * $qty;

                $items[] = [
                    'sku' => $sku,
                    'quantity' => $qty,
                    'price' => $price,
                    'sub_total' => $subTotal,
                ];

                $amount += $subTotal;
                $totalQuantity += $qty;
            }

            $payable = $amount;
            $isPaid = collect([0, 1, 2, 2, 2])->random(); // skew toward fully paid

            $purchaseOrder = new PurchaseOrder([
                'supplier_id' => $suppliers->random(),
                'date' => $date->toDateString(),
                'amount' => $amount,
                'total_quantity' => $totalQuantity,
                'payable_amount' => $payable,
                'invoice_no' => 'PI-' . str_pad($invoiceSeq++, 5, '0', STR_PAD_LEFT),
                'payment_method' => collect(['cash', 'bank'])->random(),
                'status' => 1,
                'added_to_stock' => 1,
                'is_paid' => $isPaid,
                'created_by' => $adminId,
            ]);
            $showroom->purchases()->save($purchaseOrder);

            foreach ($items as $line) {
                $sku = $line['sku'];

                $purchaseOrder->items()->save(new ProductItemDetail([
                    'product_sku_id' => $sku->id,
                    'price' => $line['price'],
                    'quantity' => $line['quantity'],
                    'tax' => 0,
                    'discount' => 0,
                    'sub_total' => $line['sub_total'],
                    'productable_id' => $sku->id,
                    'productable_type' => ProductSku::class,
                ]));

                $purchaseOrder->houses()->save(new ProductHistory([
                    'type' => 'purchase',
                    'date' => $date->toDateString(),
                    'in_out' => $line['quantity'],
                    'product_sku_id' => $sku->id,
                    'itemable_id' => $showroom->id,
                    'itemable_type' => ShowRoom::class,
                ]));

                $stock = StockReport::where('houseable_id', $showroom->id)
                    ->where('houseable_type', ShowRoom::class)
                    ->where('product_sku_id', $sku->id)
                    ->first();

                if ($stock) {
                    $stock->stock = (int) $stock->stock + $line['quantity'];
                    $stock->stock_date = $date->toDateString();
                    $stock->save();
                }
            }

            if ($isPaid !== 0) {
                $paidAmount = $isPaid === 2 ? $payable : round($payable * (rand(30, 70) / 100), 2);
                $purchaseOrder->payments()->save(new Payment([
                    'payment_method' => $purchaseOrder->payment_method,
                    'amount' => $paidAmount,
                    'payment_type' => 'pay',
                    'initial_payment' => 1,
                    'account_id' => $purchaseOrder->payment_method === 'bank' ? $bankChartAccountId : null,
                ]));
            }

            try {
                app(PurchaseOrderRepositoryInterface::class)->approve($purchaseOrder->id);
                $approved++;
            } catch (\Throwable $e) {
                $this->command->warn("Accounting post failed for purchase order #{$purchaseOrder->id} ({$purchaseOrder->invoice_no}): " . $e->getMessage());
            }
        }

        $this->command->info("Seeded {$orderCount} purchase orders with line items and stock movement ({$approved} posted to accounts).");
    }
}
