<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("locations", function (Blueprint $table) {
            $table->string("customer_ticket_status", 20)->default("unconfigured");
            $table->string("customer_ticket_payer", 20)->default("customer");
            $table->string("customer_ticket_payment_method", 20)->default("direct");
            $table->string("coach_ticket_status", 20)->default("unconfigured");
            $table->string("coach_ticket_payer", 20)->default("ragen");
            $table->boolean("full_include_enabled")->default(false);
            $table->string("full_include_name")->nullable();
            $table->decimal("full_include_price", 12, 2)->nullable();
            $table->date("full_include_start_date")->nullable();
            $table->date("full_include_end_date")->nullable();
        });
    }

    public function down(): void
    {
        Schema::table("locations", function (Blueprint $table) {
            $table->dropColumn(["customer_ticket_status", "customer_ticket_payer", "customer_ticket_payment_method", "coach_ticket_status", "coach_ticket_payer", "full_include_enabled", "full_include_name", "full_include_price", "full_include_start_date", "full_include_end_date"]);
        });
    }
};
