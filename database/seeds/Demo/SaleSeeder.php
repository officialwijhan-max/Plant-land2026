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
use Modules\Sale\Entities\Payment;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Repositories\SaleRepositoryInterface;

/**
 * Sales/invoices to landscaping clients over the last ~2 months. Same
 * approach as PurchaseSeeder: header/items direct against the models
 * rather than SaleRepository::create() (see that file's docblock for
 * why), only selling quantities the seeded stock can actually cover.
 *
 * Stock is deducted by SaleRepository::statusChange() below, NOT here -
 * that method does its own decrement, so doing it twice would silently
 * under-count stock. statusChange() is also what posts the real
 * accounting entries (journal/cash/bank vouchers) the dashboard's bank/
 * cash/profit figures read from - see PurchaseSeeder's docblock for why
 * that's called rather than reimplemented. Requires ChartAccountSeeder
 * to have run first.
 */
class SaleSeeder extends Seeder
{
    public function run()
    {
        if (Sale::count() > 0) {
            $this->command->info('sales already has data, skipping.');
            return;
        }

        $adminId = DB::table('users')->where('role_id', 1)->value('id');
        if ($adminId) {
            Auth::loginUsingId($adminId);
        }

        $showroom = ShowRoom::find(1);
        $customers = ContactModel::where('contact_type', 'Customer')->where('id', '!=', 1)->pluck('id');
        $salesAgentIds = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.name', 'Sales')
            ->pluck('users.id');
        $bankChartAccountId = BankAccount::value('chart_account_id');

        if (! $showroom || $customers->isEmpty() || ! $bankChartAccountId) {
            $this->command->warn('Missing showroom/customers/bank account - run ContactSeeder, BankAccountSeeder first.');
            return;
        }

        $orderCount = 20;
        $invoiceSeq = 1;
        $created = 0;
        $approved = 0;

        for ($i = 0; $i < $orderCount; $i++) {
            $date = now()->subDays(rand(0, 58));
            $lineCount = rand(2, 5);

            // Only pick SKUs that currently have enough stock to sell from.
            $availableStock = StockReport::where('houseable_id', $showroom->id)
                ->where('houseable_type', ShowRoom::class)
                ->where('stock', '>', 5)
                ->inRandomOrder()
                ->limit($lineCount)
                ->get();

            if ($availableStock->isEmpty()) {
                continue;
            }

            $items = [];
            $amount = 0;
            $totalQuantity = 0;

            foreach ($availableStock as $stock) {
                $sku = ProductSku::find($stock->product_sku_id);
                if (! $sku) {
                    continue;
                }

                $maxQty = max(1, min((int) $stock->stock, 20));
                $qty = rand(1, $maxQty);
                $price = $sku->selling_price;
                $subTotal = $price * $qty;

                $items[] = ['sku' => $sku, 'stock' => $stock, 'quantity' => $qty, 'price' => $price, 'sub_total' => $subTotal];
                $amount += $subTotal;
                $totalQuantity += $qty;
            }

            if (empty($items)) {
                continue;
            }

            $paymentMethod = collect(['cash', 'bank'])->random();
            $isBank = $paymentMethod === 'bank' ? 1 : 0;
            $status = collect([1, 1, 1, 2, 0])->random(); // skew toward paid

            $sale = new Sale([
                'customer_id' => $customers->random(),
                'user_id' => $salesAgentIds->isNotEmpty() ? $salesAgentIds->random() : $adminId,
                'date' => $date->toDateString(),
                'amount' => $amount,
                'total_quantity' => $totalQuantity,
                'total_discount' => 0,
                'total_tax' => 0,
                'shipping_charge' => 0,
                'other_charge' => 0,
                'payable_amount' => $amount,
                'invoice_no' => 'INV-' . str_pad($invoiceSeq++, 5, '0', STR_PAD_LEFT),
                'discount_amount' => 0,
                'discount_type' => 1,
                'status' => $status,
                'type' => 1,
                'is_approved' => 1,
                'is_bank' => $isBank,
                'created_by' => $adminId,
            ]);
            $showroom->sales()->save($sale);

            foreach ($items as $line) {
                $sku = $line['sku'];

                $sale->items()->save(new ProductItemDetail([
                    'product_sku_id' => $sku->id,
                    'price' => $line['price'],
                    'quantity' => $line['quantity'],
                    'tax' => 0,
                    'discount' => 0,
                    'sub_total' => $line['sub_total'],
                    'productable_id' => $sku->id,
                    'productable_type' => ProductSku::class,
                ]));

                $sale->houses()->save(new ProductHistory([
                    'type' => 'sales',
                    'date' => $date->toDateString(),
                    'in_out' => $line['quantity'],
                    'product_sku_id' => $sku->id,
                    'itemable_id' => $showroom->id,
                    'itemable_type' => ShowRoom::class,
                ]));
            }

            if ($status !== 0) {
                $paidAmount = $status === 1 ? $amount : round($amount * (rand(30, 70) / 100), 2);
                $sale->payments()->save(new Payment([
                    'payment_method' => $paymentMethod,
                    'amount' => $paidAmount,
                    'payment_type' => 'pay',
                    'initial_payment' => 1,
                    'account_id' => $isBank ? $bankChartAccountId : null,
                ]));
            }

            try {
                app(SaleRepositoryInterface::class)->statusChange($sale->id);
                $approved++;
            } catch (\Throwable $e) {
                $this->command->warn("Accounting post failed for sale #{$sale->id} ({$sale->invoice_no}): " . $e->getMessage());
            }

            $created++;
        }

        $this->command->info("Seeded {$created} sales/invoices with line items and stock movement ({$approved} posted to accounts).");
    }
}
