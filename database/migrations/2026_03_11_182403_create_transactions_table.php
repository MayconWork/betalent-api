<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up(){
        Schema::create('transactions', function(Blueprint $table){
            $table->id();
            // Foreign key com clients
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            // Foreign key ao Gateway utilizado
            $table->foreignId('gateway_id')->nullable()->constrained('gateways')->nullOnDelete();
            $table->string('external_id')->nullable();
            $table->string('status')->index();
            // valor em centavos
            $table->unsignedBigInteger('amount');
            $table->string('card_last_numbers')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
