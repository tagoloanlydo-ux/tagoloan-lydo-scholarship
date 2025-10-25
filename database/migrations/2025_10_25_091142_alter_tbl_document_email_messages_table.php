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
            // Drop old foreign key and unused columns
            $table->dropForeign(['document_comment_id']);
            $table->dropColumn(['document_comment_id', 'document_type', 'email_message']);

            // Add the new columns
            $table->text('email_content')->nullable(); // ✅ New column used in your code
            $table->timestamp('sent_at')->nullable(); // ✅ For tracking when the email was sent
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_document_email_messages', function (Blueprint $table) {
            // Rollback changes
            $table->unsignedBigInteger('document_comment_id');
            $table->string('document_type');
            $table->text('email_message');

            // Drop the newly added columns
            $table->dropColumn(['email_content', 'sent_at']);

            // Recreate the foreign key constraint
            $table->foreign('document_comment_id')
                  ->references('id')
                  ->on('tbl_document_comments')
                  ->onDelete('cascade');
        });
    }
};
