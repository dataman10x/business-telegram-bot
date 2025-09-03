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
        Schema::create('waitlist', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('dialog_messages', function (Blueprint $table) {
            $table->id();
            $table->json('keywords');
            $table->text('detail', 1000);
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->index(['id']);
        });

        Schema::create('broadcast_messages', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->text('detail', 1000);
            $table->boolean('can_repeat');
            $table->datetime('start_at');
            $table->datetime('end_at');
            $table->timestamps();
            $table->index(['id']);
        });

        Schema::create('direct_messages', function (Blueprint $table) {
            $table->id();
            $table->text('message', 1000);
            $table->text('reply', 1000)->nullable();
            $table->boolean('is_read')->nullable();
            $table->timestamps();
        });

        Schema::create('urls', function (Blueprint $table) {
            $table->id();
            $table->string('url_name');
            $table->string('url_link')->unique();
            $table->timestamps();
            $table->index(['id', 'url_link']);
        });

        Schema::create('visit_counters', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->integer('one_time')->default(0);
            $table->integer('daily')->default(0);
            $table->integer('monthly')->default(0);
            $table->integer('yearly')->default(0);
            $table->integer('demo')->default(0);
            $table->datetime('last_date')->nullable();
            $table->timestamps();
            $table->index(['id']);
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->text('detail', 1000);
            $table->timestamps();
            $table->index(['id']);
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->text('note', 1000)->nullable();
            $table->integer('payment')->default(0);
            $table->datetime('start_at');
            $table->datetime('end_at');
            $table->timestamps();
        });

        Schema::create('opinion_polls', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->text('detail', 1000)->nullable();
            $table->string('question');
            $table->json('options');
            $table->datetime('start_at');
            $table->datetime('end_at');
            $table->timestamps();
            $table->index(['id']);
        });

        Schema::create('opinion_poll_users', function (Blueprint $table) {
            $table->id();
            $table->string('selected');
            $table->timestamps();
        });

        Schema::create('bot_inputs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('type')->nullable();
            $table->boolean('is_active')->nullable();
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('note');
            $table->boolean('is_approved')->nullable();
            $table->timestamps();
            $table->index(['id']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('detail')->nullable();
            $table->string('contacts')->nullable();
            $table->timestamps();
        });

        Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            $table->string('file_id');
            $table->string('unique_id')->nullable();
            $table->string('path')->nullable();
            $table->string('local_path')->nullable();
            $table->string('size')->nullable();
            $table->string('mime')->nullable();
            $table->string('name')->nullable();
            $table->string('disc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waitlist');
        Schema::dropIfExists('dialog_messages');
        Schema::dropIfExists('broadcast_messages');
        Schema::dropIfExists('direct_messages');
        Schema::dropIfExists('urls');
        Schema::dropIfExists('visit_counters');
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('opinion_poll_users');
        Schema::dropIfExists('opinion_polls');
        Schema::dropIfExists('bot_inputs');
        Schema::dropIfExists('reviews');
    }
};
