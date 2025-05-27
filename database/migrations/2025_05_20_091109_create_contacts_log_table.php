<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Contact;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
    
            // Foreign key to contacts table
            $table->foreignIdFor(Contact::class)
                ->constrained()
                ->cascadeOnDelete();

            // Was the message sent succesfully
            $table->boolean('message_sent');

            // IP address & User agent
            $table->ipAddress('ip');
            $table->text('user_agent')->nullable();
            
            // Additional fields from the API
            $table->string('status'); // success or fail
            // Only if status is fail: private range, reserved range, invalid query
            $table->string('message')->nullable();

            $table->string('country')->nullable();
            $table->string('regionName')->nullable(); // Region or state
            $table->string('city')->nullable();
            $table->string('zip')->nullable(); // Zip or postal code
            $table->string('timezone')->nullable();

            // Latitude and Longitude Coordinates
            $table->decimal('lat', 12, 9)->nullable();
            $table->decimal('lon', 12, 9)->nullable();

            // Internet service provider
            $table->string('isp')->nullable(); // Internet Service Provider
            $table->string('as')->nullable(); // AS number and organization
            $table->string('reverse')->nullable(); // Reverse DNS of the IP

            $table->boolean('mobile')->default(false); // Mobile cellular connection was used
            $table->boolean('proxy')->default(false); // Indicates if a proxy, VPN or Tor was used
            $table->boolean('hosting')->default(false); // Indicates if it's a hosting provider
            
            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_logs');
    }
};
