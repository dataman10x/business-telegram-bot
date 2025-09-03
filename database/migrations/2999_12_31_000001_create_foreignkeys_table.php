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
        Schema::table('waitlist', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('user')->onDelete('cascade')->onUpdate('cascade');
            $table->index(['id', 'user_id']);
        });

        Schema::table('direct_messages', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('user')->onDelete('cascade')->onUpdate('cascade');
            $table->index(['id', 'user_id']);
        });

        Schema::table('visit_counters', function (Blueprint $table) {
            $table->foreign('id')->references('id')->on('user')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('user')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade')->onUpdate('cascade');
            $table->index(['id', 'user_id']);
        });

        Schema::table('opinion_poll_users', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('user')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('poll_id')->constrained('opinion_polls')->onDelete('cascade')->onUpdate('cascade');
            $table->index(['id', 'poll_id', 'user_id']);
        });

        Schema::table('bot_inputs', function (Blueprint $table) {
            $table->foreign('id')->references('id')->on('user')->onDelete('cascade')->onUpdate('cascade');
            $table->index(['id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('id')->references('id')->on('user')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('user')->onDelete('cascade')->onUpdate('cascade');
            $table->index(['id', 'user_id']);
        });

        Schema::table('product_media', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade')->onUpdate('cascade');
            $table->index(['id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waitlist', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('direct_messages', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('visit_counters', function (Blueprint $table) {
            $table->dropForeign(['id']);
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['subscription_id']);
        });

        Schema::table('opinion_poll_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['poll_id']);
        });

        Schema::table('bot_inputs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['id']);
        });

        Schema::table('product_media', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
