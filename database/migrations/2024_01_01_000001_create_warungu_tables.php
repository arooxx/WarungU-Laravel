<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_warung', function (Blueprint $table) {
            $table->id('id_warung');
            $table->string('nama_warung', 100);
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('tb_users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama', 100);
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->enum('role', ['pemilik', 'pegawai'])->default('pegawai');
            $table->string('remember_token', 255)->nullable();
            $table->unsignedBigInteger('id_warung');
            $table->timestamps();
            $table->foreign('id_warung')->references('id_warung')->on('tb_warung')->onDelete('cascade');
        });

        Schema::create('tb_kategori', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori', 100);
            $table->timestamps();
        });

        Schema::create('tb_barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->string('barcode', 100)->nullable();
            $table->string('nama_barang', 100);
            $table->integer('harga_beli')->default(0);
            $table->integer('harga_jual')->default(0);
            $table->integer('harga_satuan')->default(0);
            $table->integer('isi_per_pack')->default(1);
            $table->enum('bisa_satuan', ['ya', 'tidak'])->default('tidak');
            $table->integer('stok')->default(0);
            $table->string('satuan', 20)->nullable();
            $table->string('foto', 255)->nullable();
            $table->unsignedBigInteger('id_kategori');
            $table->unsignedBigInteger('id_warung');
            $table->timestamps();
            $table->foreign('id_kategori')->references('id_kategori')->on('tb_kategori');
            $table->foreign('id_warung')->references('id_warung')->on('tb_warung');
        });

        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->dateTime('tanggal');
            $table->integer('total')->default(0);
            $table->integer('bayar')->default(0);
            $table->integer('kembalian')->default(0);
            $table->enum('metode_pembayaran', ['cash', 'qris'])->default('cash');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_warung');
            $table->timestamps();
            $table->foreign('id_user')->references('id_user')->on('tb_users');
            $table->foreign('id_warung')->references('id_warung')->on('tb_warung');
        });

        Schema::create('tb_detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_barang');
            $table->enum('tipe_penjualan', ['pack', 'satuan'])->default('pack');
            $table->integer('qty');
            $table->integer('harga');
            $table->integer('subtotal');
            $table->timestamps();
            $table->foreign('id_transaksi')->references('id_transaksi')->on('tb_transaksi')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('tb_barang');
        });

        Schema::create('tb_stok_masuk', function (Blueprint $table) {
            $table->id('id_stok');
            $table->unsignedBigInteger('id_barang');
            $table->integer('jumlah');
            $table->dateTime('tanggal');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('id_user');
            $table->timestamps();
            $table->foreign('id_barang')->references('id_barang')->on('tb_barang');
            $table->foreign('id_user')->references('id_user')->on('tb_users');
        });

        Schema::create('tb_log_aktivitas', function (Blueprint $table) {
            $table->id('id_log');
            $table->text('aktivitas');
            $table->unsignedBigInteger('id_user');
            $table->dateTime('tanggal');
            $table->timestamps();
            $table->foreign('id_user')->references('id_user')->on('tb_users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_log_aktivitas');
        Schema::dropIfExists('tb_stok_masuk');
        Schema::dropIfExists('tb_detail_transaksi');
        Schema::dropIfExists('tb_transaksi');
        Schema::dropIfExists('tb_barang');
        Schema::dropIfExists('tb_kategori');
        Schema::dropIfExists('tb_users');
        Schema::dropIfExists('tb_warung');
    }
};
