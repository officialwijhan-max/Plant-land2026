<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Setup\Entities\IntroPrefix;

class AddIntroPrefixForExtraUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        IntroPrefix::create([ 'prefix' => 'AGE', 'title' => 'About Agency']); // 10
        IntroPrefix::create([ 'prefix' => 'SP', 'title' => 'About Strategic Partner']);  // 11
        IntroPrefix::create([ 'prefix' => 'BEN', 'title' => 'About Benches']);  // 12
        IntroPrefix::create([ 'prefix' => 'KIO', 'title' => 'About Kiosks']);  // 13
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
