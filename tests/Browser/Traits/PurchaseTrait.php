<?php


namespace Tests\Browser\Traits;


use App\Repositories\UserRepository;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Session;
use Modules\Account\Entities\Voucher;
use Modules\Contact\Repositories\ContactRepository;
use Modules\Inventory\Entities\ShowRoom;
use Modules\Inventory\Entities\StockAdjustment;
use Modules\Inventory\Entities\StockTransfer;
use Modules\Inventory\Entities\WareHouse;
use Modules\Inventory\Http\Controllers\ShowRoomController;
use Modules\Inventory\Repositories\ShowRoomRepository;
use Modules\Product\Entities\Brand;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\ModelType;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductHistory;
use Modules\Product\Entities\ProductSku;
use Modules\Product\Entities\UnitType;
use Modules\Product\Entities\Variant;
use Modules\Product\Entities\VariantValues;
use Modules\Product\Repositories\ProductRepository;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Repositories\PurchaseOrderRepository;
use Modules\Sale\Entities\Sale;
use Modules\Setting\Model\BusinessSetting;
use Modules\Setting\Model\GeneralSetting;
use phpDocumentor\Reflection\Types\Null_;

trait PurchaseTrait
{
    use withFaker;

    public function createCategory()
    {
        Category::insert([
            [
                'id' => 1,
                'name' => 'Test Category',
                'status' => 1,
                'code' => 'tc',
                'level' => 0,
                'parent_id' => null
            ],
            [
                'id' => 2,
                'name' => 'Test Category 02',
                'status' => 1,
                'code' => 'tc-02',
                'level' => 0,
                'parent_id' => null
            ],
            [
                'id' => 3,
                'name' => 'Test Sub Category',
                'status' => 1,
                'level' => 1,
                'code' => 'tsc',
                'parent_id' => 1
            ],
            [
                'id' => 4,
                'name' => 'Test Sub Category 02',
                'status' => 1,
                'level' => 1,
                'code' => 'tsc-02',
                'parent_id' => 2
            ],
        ]);
    }

    public function createModel()
    {
        ModelType::insert([
            [
                'id' => 1,
                'name' => 'Test Model',
                'status' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Test Model 02',
                'status' => 1,
            ]
        ]);
    }

    public function createVariant()
    {
        $variant = Variant::create([
            'id' => 1,
            'name' => 'Color',
            'status' => 1
        ]);
        VariantValues::insert([
            [
                'id' => 1,
                'variant_id' => $variant->id,
                'value' => 'Red'
            ],
            [
                'id' => 2,
                'variant_id' => $variant->id,
                'value' => 'Green'
            ],
            [
                'id' => 3,
                'variant_id' => $variant->id,
                'value' => 'Yellow'
            ]

        ]);

        $variant = Variant::create([
            'id' => 2,
            'name' => 'Size',
            'status' => 1
        ]);
        VariantValues::insert([
            [
                'id' => 4,
                'variant_id' => $variant->id,
                'value' => '32'
            ],
            [
                'id' => 5,
                'variant_id' => $variant->id,
                'value' => '34'
            ],
            [
                'id' => 6,
                'variant_id' => $variant->id,
                'value' => '36'
            ]

        ]);
    }

    public function createUnitType()
    {
        UnitType::insert([
            [
                'id' => 1,
                'name' => 'Test Unit Type',
                'status' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Test Unit Type 02',
                'status' => 1,
            ]
        ]);
    }

    public function createBrand()
    {
        Brand::insert([
            [
                'id' => 1,
                'name' => 'Test Brand',
                'status' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Test Brand 02',
                'status' => 1,
            ]
        ]);
    }

    public function createProduct()
    {
        $this->setApp();
        $this->createBrand();
        $this->createCategory();
        $this->createModel();
        $this->createUnitType();
        $this->createVariant();
        $productRepo = new ProductRepository();

//        Single product
        $productRepo->create([
            'product_type' => 'Single',
            'product_name' => 'Test Product',
            'product_sku' => 'vs-01',
            'origin' => NULL,
            'unit_type_id' => '1',
            'barcode_type' => 'C39',
            'brand_id' => '1',
            'category_id' => '1',
            'sub_category_id' => '3',
            'model_id' => '1',
            'alert_quantity' => '10',
            'purchase_price' => '90',
            'selling_price' => '100',
            'hourly_rate' => '0',
            'min_selling_price' => '95',
            'price_of_other_currency' => '150',
            'tax' => '0',
            'tax_type' => '%',
            'product_description' => NULL,
            'description' => NULL,
        ]);

//        Variant Product
        $productRepo->create([
            'product_type' => 'Variable',
            'product_name' => 'Test Variant Product',
            'origin' => NULL,
            'unit_type_id' => '1',
            'barcode_type' => 'C39',
            'brand_id' => '1',
            'category_id' => '1',
            'sub_category_id' => '3',
            'model_id' => '1',
            'hourly_rate' => '0',
            'min_selling_price' => '0',
            'price_of_other_currency' => '100',
            'tax' => '0',
            'tax_type' => '%',
            'product_description' => NULL,
            'description' => NULL,
            'selected_variant' =>
                array(
                    0 => '2',
                    1 => '1',
                ),
            'variation_type' =>
                array(
                    0 => '2',
                    1 => '1',
                    2 => '2',
                    3 => '1',
                    4 => '2',
                    5 => '1',
                    6 => '2',
                    7 => '1',
                    8 => '2',
                    9 => '1',
                    10 => '2',
                    11 => '1',
                    12 => '2',
                    13 => '1',
                    14 => '2',
                    15 => '1',
                    16 => '2',
                    17 => '1',
                ),
            'variation_value_id' =>
                array(
                    0 => '4',
                    1 => '1',
                    2 => '4',
                    3 => '2',
                    4 => '4',
                    5 => '3',
                    6 => '5',
                    7 => '1',
                    8 => '5',
                    9 => '2',
                    10 => '5',
                    11 => '3',
                    12 => '6',
                    13 => '1',
                    14 => '6',
                    15 => '2',
                    16 => '6',
                    17 => '3',
                ),
            'variation_sku' =>
                array(
                    0 => 'v-01',
                    1 => 'v-02',
                    2 => 'v-03',
                    3 => 'v-04',
                    4 => 'v-05',
                    5 => 'v-06',
                    6 => 'v-07',
                    7 => 'v-08',
                    8 => 'v-09',
                ),
            'alert_quantities' =>
                array(
                    0 => '10',
                    1 => '10',
                    2 => '10',
                    3 => '10',
                    4 => '10',
                    5 => '10',
                    6 => '10',
                    7 => '10',
                    8 => '10',
                ),
            'purchase_prices' =>
                array(
                    0 => '90',
                    1 => '0',
                    2 => '90',
                    3 => '0',
                    4 => '90',
                    5 => '0',
                    6 => '90',
                    7 => '0',
                    8 => '90',
                ),
            'min_selling_prices' =>
                array(
                    0 => '95',
                    1 => '0',
                    2 => '95',
                    3 => '0',
                    4 => '95',
                    5 => '0',
                    6 => '95',
                    7 => '0',
                    8 => '95',
                ),
            'selling_prices' =>
                array(
                    0 => '100',
                    1 => '0',
                    2 => '100',
                    3 => '0',
                    4 => '100',
                    5 => '0',
                    6 => '100',
                    7 => '0',
                    8 => '100',
                ),
        ]);

        $this->addToStock();

//        Combo Product

        $productRepo->create([
            'product_type' => 'Combo',
            'product_name' => 'Test Combo Product',
            'origin' => NULL,
            'unit_type_id' => 'Select Unit',
            'barcode_type' => 'C39',
            'brand_id' => 'Select Brand',
            'category_id' => NULL,
            'sub_category_id' => 'Select Sub Category',
            'model_id' => 'Select Model',
            'selected_product_id' =>
                array(
                    0 => '10',
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                ),
            'purchase_price' => '270',
            'selling_price' => '300',
            'hourly_rate' => '0',
            'min_selling_price' => '300',
            'combo_selling_price' => '350',
            'price_of_other_currency' => '0',
            'tax' => '0',
            'tax_type' => '%',
            'product_description' => NULL,
            'description' => NULL,
            'selected_product_qty' =>
                array(
                    0 => '1',
                    1 => '1',
                    2 => '1',
                    3 => '1',
                    4 => '1',
                ),
            'selected_product_price' =>
                array(
                    0 => '100',
                    1 => '0',
                    2 => '100',
                    3 => '0',
                    4 => '100',
                ),
            'selected_product_tax' =>
                array(
                    0 => '0',
                    1 => '0',
                    2 => '0',
                    3 => '0',
                    4 => '0',
                ),
        ]);
    }

    public function setApp()
    {
        (new \SpondonIt\BizService\Repositories\InitRepository())->config();
    }

    public function addToStock()
    {

        $product_skus = ProductSku::all();

        $openingStockRepo = new PurchaseOrderRepository();

        foreach ($product_skus as $sku) {
            $openingStockRepo->adToStockOpening([
                'product_sku_id' => $sku->id,
                'stock_date' => date('m/d/Y'),
                'stock_quantity' => '100',
                'showroom' => 'showroom-1',
                'purchase_price' => $sku->purchase_price,
                'selling_price' => $sku->selling_price,
                'serial_no' => NULL,
            ]);
        }
    }

    public function createSupplier()
    {
        $contact_repo = new ContactRepository();
        $contact_repo->create([
            'contact_type' => 'Supplier',
            'name' => 'Tariqul islam (Suplier)',
            'business_name' => 'Spondonit',
            'tax_number' => 'jdklj445545',
            'opening_balance' => '5000',
            'pay_term' => NULL,
            'pay_term_condition' => 'Months',
            'credit_limit' => NULL,
            'customer_group' => 'None',
            'email' => NULL,
            'mobile' => NULL,
            'alternate_contact_no' => NULL,
            'address' => NULL,
            'note' => NULL,
        ]);
    }

    public function createCustomer()
    {
        $contact_repo = new ContactRepository();
        $contact_repo->create([
            'contact_type' => 'Customer',
            'name' => 'Tariqul islam (Customer)',
            'business_name' => 'Spondonit',
            'tax_number' => 'jdklj445545',
            'opening_balance' => '5000',
            'pay_term' => NULL,
            'pay_term_condition' => 'Months',
            'credit_limit' => NULL,
            'customer_group' => 'None',
            'email' => NULL,
            'mobile' => NULL,
            'alternate_contact_no' => NULL,
            'address' => NULL,
            'note' => NULL,
        ]);
    }

    public function clearTable()
    {

        $purchases = PurchaseOrder::all();
        foreach ($purchases as $purchase) {
            $purchase->items()->delete();
            $purchase->costs()->delete();
            $purchase->payments()->delete();
            $purchase->delete();
        }

        $sales = Sale::all();
        foreach ($sales as $sale) {
            $sale->items()->delete();
            $sale->payments()->delete();
            $sale->delete();
        }
        $products = Product::all();
        foreach ($products as $product) {
            $product->skus()->delete();
            $product->variations()->delete();
            $product->delete();
        }

        $categories = Category::all();
        foreach ($categories as $category) {
            $category->delete();
        }
        $models = ModelType::get();
        foreach ($models as $model) {
            $model->delete();
        }
        $unitTypes = UnitType::get();
        foreach ($unitTypes as $unitType) {
            $unitType->delete();
        }
        $brands = Brand::get();
        foreach ($brands as $brand) {
            $brand->delete();
        }

        $variants = Variant::get();
        foreach ($variants as $variant) {
            $variant->values()->delete();
            $variant->delete();
        }

        $branches = ShowRoom::where('id', '!=', 1)->get();
        foreach ($branches as $branch){
            $branch->delete();
        }

        $warehouses = WareHouse::get();
        foreach ($warehouses as $warehouse){
            $warehouse->delete();
        }

        $transfers = StockTransfer::get();
        foreach ($transfers as $transfer){
            $transfer->items()->delete();
            $transfer->delete();
        }

        $skus = ProductSku::get();
        foreach ($skus as $sku){
            $sku->stocks()->delete();
            $sku->costOfGoodsPrices()->delete();
            $sku->delete();
        }

        $histories = ProductHistory::get();
        foreach ($histories as $history){
            $history->delete();
        }

        $stock_adjs = StockAdjustment::get();
        foreach ($stock_adjs as $stock_adj){
            $stock_adj->delete();
        }

    }

    public function createShowRoom()
    {
        $showRoomRepo = new ShowRoomRepository();
        $showRoomRepo->create([
            "name" => $this->faker->name,
            "email" => $this->faker->email,
            "phone" => $this->faker->phoneNumber,
            "status" => "1",
            "address" => 'Dhaka, Bangladesh',
        ]);
    }

    public function clearVoucherTable(){
        $branches = ShowRoom::where('id', '!=', 1)->get();
        foreach ($branches as $branch){
            $branch->delete();
        }
        $vouchers = Voucher::all();
        foreach ($vouchers as $voucher){
            $voucher->transactions()->delete();
            $voucher->delete();
        }
    }

    public function createStaff(){
        $userRepo = new UserRepository();
       $userRepo->store(array ('role_id' => '2-regular_user', 'name' => $this->faker->name, 'email' => $this->faker->email, 'username' => NULL, 'password' => '12345678', 'department_id' => '1', 'showroom_id' => '1', 'date_of_birth' => NULL, 'current_address' => NULL, 'permanent_address' => NULL, 'opening_balance' => NULL, 'role_type' => NULL, 'leave_applicable_date' => '07/17/2021', 'bank_name' => 'DBBL', 'bank_branch_name' => 'Uta Carney', 'bank_account_name' => 'Ava Hoffman', 'bank_account_no' => '55420213254', 'date_of_joining' => '07/17/2021', 'basic_salary' => '30000', 'employment_type' => 'Permanent', ));
    }

    public function deleteStaff(){
        $userRepo = new UserRepository();
       $users = User::where('id', '!=', 1)->get();
       foreach($users as $user){
           $userRepo->delete($user->id);
       }
    }


}
