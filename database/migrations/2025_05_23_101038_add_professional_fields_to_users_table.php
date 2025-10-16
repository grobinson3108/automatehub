<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_professional')->default(false)->after('email');
            $table->string('company_name')->nullable()->after('is_professional');
            $table->string('company_address')->nullable()->after('company_name');
            $table->string('company_postal_code')->nullable()->after('company_address');
            $table->string('company_city')->nullable()->after('company_postal_code');
            $table->string('company_country')->nullable()->after('company_city');
            $table->string('company_vat')->nullable()->after('company_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_professional',
                'company_name',
                'company_address',
                'company_postal_code',
                'company_city',
                'company_country',
                'company_vat'
            ]);
        });
    }
};
