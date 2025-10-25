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
        Schema::table('tbl_document_email_messages', function (Blueprint $table) {
            $table->dropForeign(['document_comment_id']);
            $table->dropColumn('document_comment_id');
            $table->dropColumn('document_type');
            $table->dropColumn('email_message');
            $table->timestamp('sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_document_email_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('document_comment_id');
            $table->string('document_type');
            $table->text('email_message');
            $table->dropColumn('sent_at');

            $table->foreign('document_comment_id')
                  ->references('id')
                  ->on('tbl_document_comments')
                  ->onDelete('cascade');
        });
    }
};
