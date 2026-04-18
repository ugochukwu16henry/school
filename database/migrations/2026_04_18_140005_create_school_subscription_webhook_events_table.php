<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolSubscriptionWebhookEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_subscription_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_subscription_id')->nullable();
            $table->string('provider');
            $table->string('event_type')->nullable();
            $table->string('provider_reference')->nullable()->index();
            $table->longText('payload');
            $table->boolean('signature_valid')->default(false);
            $table->string('status_after')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('school_subscription_id', 'sswe_subscription_fk')
                ->references('id')
                ->on('school_subscriptions')
                ->nullOnDelete();
            $table->index(['provider', 'event_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_subscription_webhook_events');
    }
}
