<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\ShowRoom;
use Modules\Inventory\Entities\WareHouse;
use Modules\ProAccount\Entities\Leadger;
use Modules\Setup\Entities\Tax;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pro_leadgers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->unique();
            $table->string('type', 255)->nullable()->comment('1 => Asset, 2 => Liability, 3 => Expense, 4 => Income, 5 => Equity');
            $table->string('name', 255)->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->tinyInteger("level")->default(0);
            $table->string('morphable_type', 255)->nullable();
            $table->unsignedBigInteger('morphable_id')->nullable();
            $table->boolean("is_active")->default(1);
            $table->boolean("is_cost_center")->default(0);
            $table->boolean("is_blocked")->default(0);
            $table->string("acc_type", 15)->default("no")->nullable();
            $table->text("description")->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->index(['type', 'is_active']);
            $table->timestamps();
        });


        DB::table('pro_leadgers')->insert(array (
            0 =>
            array (

              'code' => 'A-101',
              'type' => '1',
              'name' => 'Assets',
              'parent_id' => 0,
              'level' => 1,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            1 =>
            array (

              'code' => 'CC-A-1001',
              'type' => '1',
              'name' => 'Current Asset',
              'parent_id' => 1,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            2 =>
            array (

              'code' => 'CC-B-1001',
              'type' => '1',
              'name' => 'Bank Account',
              'parent_id' => 1,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            3 =>
            array (

              'code' => 'CC-C-1001',
              'type' => '1',
              'name' => 'Cash In Hand',
              'parent_id' => 1,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            4 =>
            array (

              'code' => 'CC-D-1001',
              'type' => '1',
              'name' => 'Deposite',
              'parent_id' => 1,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            5 =>
            array (

              'code' => 'CC-E-1001',
              'type' => '1',
              'name' => 'Loans & Advance (Asset)',
              'parent_id' => 1,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            6 =>
            array (

              'code' => 'CC-A-1002',
              'type' => '1',
              'name' => 'Inventory / Current Stock',
              'parent_id' => 2,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            7 =>
            array (

              'code' => 'CC-A-1003',
              'type' => '1',
              'name' => 'Sundry Debitors',
              'parent_id' => 2,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            8 =>
            array (

              'code' => 'CC-F-1001',
              'type' => '1',
              'name' => 'Fixed Asset',
              'parent_id' => 1,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            9 =>
            array (

              'code' => 'CC-G-1001',
              'type' => '1',
              'name' => 'Investment',
              'parent_id' => 1,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            10 =>
            array (

              'code' => 'CC-H-1001',
              'type' => '1',
              'name' => 'Misc. Expenses (Asset)',
              'parent_id' => 1,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            11 =>
            array (

              'code' => 'L-201',
              'type' => '2',
              'name' => 'Liabilities',
              'parent_id' => 0,
              'level' => 1,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            12 =>
            array (

              'code' => 'CC-I-1001',
              'type' => '1',
              'name' => 'Branch / Divisions',
              'parent_id' => 1,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            13 =>
            array (

              'code' => 'L-202',
              'type' => '2',
              'name' => 'Capital Account',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            14 =>
            array (

              'code' => 'L-203',
              'type' => '2',
              'name' => 'Reserve & Surplus',
              'parent_id' => 14,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            15 =>
            array (

              'code' => 'CC-A-2001',
              'type' => '2',
              'name' => 'Current Liabilities',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            16 =>
            array (

              'code' => 'CC-B-2001',
              'type' => '2',
              'name' => 'Duties and Taxes',
              'parent_id' => 16,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            17 =>
            array (

              'code' => 'CC-C-2001',
              'type' => '2',
              'name' => 'Provisions',
              'parent_id' => 16,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            18 =>
            array (

              'code' => 'L-204',
              'type' => '2',
              'name' => 'Sundry Creator',
              'parent_id' => 16,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            19 =>
            array (

              'code' => 'CC-D-2001',
              'type' => '2',
              'name' => 'Loans (Liabilities)',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            20 =>
            array (

              'code' => 'CC-E-2001',
              'type' => '2',
              'name' => 'Bank OD Acc',
              'parent_id' => 20,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            21 =>
            array (

              'code' => 'CC-F-2001',
              'type' => '2',
              'name' => 'Secure Loans',
              'parent_id' => 20,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            22 =>
            array (

              'code' => 'CC-G-2001',
              'type' => '2',
              'name' => 'Unsecured Loans',
              'parent_id' => 20,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            23 =>
            array (

              'code' => 'CC-H-2001',
              'type' => '2',
              'name' => 'Suspense Acc',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            24 =>
            array (

              'code' => 'L-205',
              'type' => '2',
              'name' => 'Profit Loss Acc',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            25 =>
            array (

              'code' => 'EXP-301',
              'type' => '3',
              'name' => 'Expense',
              'parent_id' => 0,
              'level' => 1,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            26 =>
            array (

              'code' => 'CC-A-3001',
              'type' => '3',
              'name' => 'Direct Expense',
              'parent_id' => 26,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            27 =>
            array (

              'code' => 'CC-B-3001',
              'type' => '3',
              'name' => 'Indirect Expense',
              'parent_id' => 26,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            28 =>
            array (

              'code' => 'INC-401',
              'type' => '4',
              'name' => 'Income',
              'parent_id' => 0,
              'level' => 1,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            29 =>
            array (

              'code' => 'CC-A-4001',
              'type' => '4',
              'name' => 'Direct Income',
              'parent_id' => 29,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            30 =>
            array (

              'code' => 'CC-B-4001',
              'type' => '4',
              'name' => 'Indirect Income',
              'parent_id' => 29,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            31 =>
            array (

              'code' => 'CC-C-1002',
              'type' => '1',
              'name' => 'Cash',
              'parent_id' => 4,
              'level' => 4,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'cash',
            ),
            32 =>
            array (

              'code' => 'L-206',
              'type' => '2',
              'name' => 'Due Salaries & Wages',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            33 =>
            array (

              'code' => 'CC-B-3002',
              'type' => '3',
              'name' => 'Salary Expenses - Office Staff & Management',
              'parent_id' => 28,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            34 =>
            array (

              'code' => 'CC-E-1002',
              'type' => '1',
              'name' => 'Employee Advance & Loan',
              'parent_id' => 6,
              'level' => 4,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            35 =>
            array (

              'code' => 'CC-A-4002',
              'type' => '4',
              'name' => 'Default Sales AC',
              'parent_id' => 31,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            36 =>
            array (

              'code' => 'CC-A-4003',
              'type' => '4',
              'name' => 'Shipping & Others Charge Income',
              'parent_id' => 31,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            37 =>
            array (

              'code' => 'CC-A-3002',
              'type' => '3',
              'name' => 'Shipping & Others Charge Expense',
              'parent_id' => 27,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            38 =>
            array (

              'code' => 'CC-B-3003',
              'type' => '3',
              'name' => 'Sales Return AC',
              'parent_id' => 28,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            39 =>
            array (

              'code' => 'CC-A-3003',
              'type' => '3',
              'name' => 'Cost of Goods Sold AC',
              'parent_id' => 27,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            40 =>
            array (

              'code' => 'L-207',
              'type' => '2',
              'name' => 'Opening Stock AC',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            41 =>
            array (

              'code' => 'EXP-302',
              'type' => '3',
              'name' => 'Income Summary Debit',
              'parent_id' => 26,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            42 =>
            array (

              'code' => 'L-208',
              'type' => '2',
              'name' => 'Product Tax AC',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            43 =>
            array (

              'code' => 'L-209',
              'type' => '2',
              'name' => 'SGST TAX',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            44 =>
            array (

              'code' => 'GST-201',
              'type' => '2',
              'name' => 'CGST TAX',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            45 =>
            array (

              'code' => 'GST-202',
              'type' => '2',
              'name' => 'IGST TAX',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            46 =>
            array (

              'code' => 'GST-203',
              'type' => '2',
              'name' => 'CESS TAX',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            47 =>
            array (

              'code' => 'CTX-204',
              'type' => '2',
              'name' => 'Company Tax Payable',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            48 =>
            array (

              'code' => 'L-210',
              'type' => '2',
              'name' => 'Retail Earning AC',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            49 =>
            array (

              'code' => 'EXP-303',
              'type' => '3',
              'name' => 'Company Income TAX',
              'parent_id' => 26,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            50 =>
            array (

              'code' => 'CC-A-3004',
              'type' => '3',
              'name' => 'Retailer / Affiliator Commission',
              'parent_id' => 27,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            51 =>
            array (

              'code' => 'L-211',
              'type' => '2',
              'name' => 'Employee Tax Payable',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            52 =>
            array (

              'code' => 'CC-B-4002',
              'type' => '4',
              'name' => 'Purchase Discount Receive',
              'parent_id' => 32,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            53 =>
            array (

              'code' => 'CC-B-3004',
              'type' => '3',
              'name' => 'Sales Discount Send',
              'parent_id' => 28,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            54 =>
            array (

              'code' => 'CC-B-3005',
              'type' => '3',
              'name' => 'Warranty Expense',
              'parent_id' => 28,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            55 =>
            array (

              'code' => 'L-212',
              'type' => '2',
              'name' => 'Eastimated Warranty Liability',
              'parent_id' => 12,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            56 =>
            array (

              'code' => 'CC-A-3005',
              'type' => '3',
              'name' => 'Expense AC',
              'parent_id' => 27,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            57 =>
            array (

              'code' => 'CC-A-4004',
              'type' => '4',
              'name' => 'Cash Income AC',
              'parent_id' => 31,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            58 =>
            array (

              'code' => 'E-501',
              'type' => '2',
              'name' => 'Equities',
              'parent_id' => 0,
              'level' => 1,
              'is_active' => 1,
              'is_cost_center' => 1,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            59 =>
            array (

              'code' => 'E-502',
              'type' => '2',
              'name' => 'Opening Balance Equity',
              'parent_id' => 60,
              'level' => 2,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            60 =>
            array (

              'code' => 'CC-A-3006',
              'type' => '3',
              'name' => 'Stock Adjustment Expense',
              'parent_id' => 27,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            61 =>
            array (

              'code' => 'CC-A-4005',
              'type' => '4',
              'name' => 'Stock Adjustment Income',
              'parent_id' => 31,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            62 =>
            array (

              'code' => 'AL-201',
              'type' => '2',
              'name' => 'Affiliator',
              'parent_id' => 16,
              'level' => 3,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'others',
            ),
            63 =>
            array (
              'code' => 'OT-1001',
              'type' => '1',
              'name' => 'Online Money Transactions',
              'parent_id' => 4,
              'level' => 4,
              'is_active' => 1,
              'is_cost_center' => 0,
              'is_blocked' => 0,
              'acc_type' => 'bank',
            ),
        ));
        
        $showrooms = Showroom::get();

        foreach ($showrooms as $key => $showroom) {
          $chart_account = new Leadger;
          $chart_account->code = $showroom->name.' - '. sprintf("%03d", $showroom->id);
          $chart_account->level = 3;
          $chart_account->is_cost_center = 0;
          $chart_account->name = 'Cash in '.$showroom->name;
          $chart_account->description = null;
          $chart_account->is_active = 1;
          $chart_account->parent_id = 13;
          $chart_account->type = 1;
          $chart_account->acc_type = "cash";
          $chart_account->morphable_type = get_class(new ShowRoom);
          $chart_account->morphable_id = $showroom->id;
          $chart_account->save();
        }

        $warehouses = WareHouse::get();

        foreach ($warehouses as $key => $warehouse) {
          $chart_account = new Leadger;
          $chart_account->code = $warehouse->name.' - '. sprintf("%03d", $warehouse->id);
          $chart_account->level = 3;
          $chart_account->is_cost_center = 0;
          $chart_account->name = $warehouse->name;
          $chart_account->description = null;
          $chart_account->is_active = 1;
          $chart_account->parent_id = 13;
          $chart_account->type = 1;
          $chart_account->acc_type = "cash";
          $chart_account->morphable_type = get_class(new WareHouse);
          $chart_account->morphable_id = $warehouse->id;
          $chart_account->save();
        }

        $taxes = Tax::get();
        foreach ($taxes as $key => $tax) {
            $chart_account = new Leadger;
            $chart_account->code = $tax->name.' - '. sprintf("%03d", $tax->id);
            $chart_account->level = 4;
            $chart_account->is_cost_center = 0;
            $chart_account->name = $tax->name;
            $chart_account->description = null;
            $chart_account->is_active = 1;
            $chart_account->parent_id = 17;
            $chart_account->type = 2;
            $chart_account->acc_type = "others";
            $chart_account->morphable_type = get_class($tax);
            $chart_account->morphable_id = $tax->id;
            $chart_account->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pro_leadgers');
    }
};
