-- LANGKAH 2 dari 3: tambah 54 jenis barang ke SEMUA warung.
-- Barang yang sudah ada (nama atau barcode sama) dilewati; data lama tidak berubah.
-- Tempel seluruh isi file ini ke SQL Editor TiDB, lalu klik Run. Jalankan SETELAH langkah 1.

INSERT INTO dbwarungu.tb_barang
    (barcode, nama_barang, harga_beli, harga_jual, harga_satuan, isi_per_pack, bisa_satuan,
     stok, satuan, foto, id_kategori, id_warung, created_at, updated_at)
SELECT p.barcode, p.nama, p.beli, p.jual, p.satuan_harga, p.isi, p.bisa,
       p.stok, p.unit, NULL,
       (SELECT c.id_kategori FROM dbwarungu.tb_kategori c
         WHERE CONVERT(LOWER(TRIM(c.nama_kategori)) USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(LOWER(TRIM(p.kat)) USING utf8mb4) COLLATE utf8mb4_unicode_ci
         ORDER BY c.id_kategori LIMIT 1),
       w.id_warung, NOW(), NOW()
FROM dbwarungu.tb_warung w
CROSS JOIN (
    SELECT 'Aqua 600ml' AS nama, 'Minuman' AS kat, '2000000000015' AS barcode, 3400 AS beli, 4000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 72 AS stok, 'botol' AS unit
    UNION ALL
    SELECT 'Aqua 1500ml' AS nama, 'Minuman' AS kat, '2000000000022' AS barcode, 5800 AS beli, 7000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 36 AS stok, 'botol' AS unit
    UNION ALL
    SELECT 'Le Minerale 600ml' AS nama, 'Minuman' AS kat, '2000000000039' AS barcode, 3400 AS beli, 4000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 48 AS stok, 'botol' AS unit
    UNION ALL
    SELECT 'Pucuk Harum Teh 350ml' AS nama, 'Minuman' AS kat, '2000000000046' AS barcode, 3700 AS beli, 4500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 48 AS stok, 'botol' AS unit
    UNION ALL
    SELECT 'Teh Gelas Original 170ml' AS nama, 'Minuman' AS kat, '2000000000053' AS barcode, 1200 AS beli, 1500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 96 AS stok, 'gelas' AS unit
    UNION ALL
    SELECT 'Coca-Cola 390ml' AS nama, 'Minuman' AS kat, '2000000000060' AS barcode, 5400 AS beli, 6500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 36 AS stok, 'botol' AS unit
    UNION ALL
    SELECT 'You C1000 Lemon 500ml' AS nama, 'Minuman' AS kat, '2000000000077' AS barcode, 8300 AS beli, 9500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'botol' AS unit
    UNION ALL
    SELECT 'Good Day Cappuccino 250ml' AS nama, 'Minuman' AS kat, '2000000000084' AS barcode, 6300 AS beli, 7500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'botol' AS unit
    UNION ALL
    SELECT 'Kapal Api Special Mix (renceng 10)' AS nama, 'Minuman' AS kat, '2000000000091' AS barcode, 19500 AS beli, 21500 AS jual, 2500 AS satuan_harga, 10 AS isi, 'ya' AS bisa, 120 AS stok, 'sachet' AS unit
    UNION ALL
    SELECT 'Good Day Cappuccino 3in1 (renceng 10)' AS nama, 'Minuman' AS kat, '2000000000107' AS barcode, 16500 AS beli, 18500 AS jual, 2000 AS satuan_harga, 10 AS isi, 'ya' AS bisa, 100 AS stok, 'sachet' AS unit
    UNION ALL
    SELECT 'Kapal Api Special 6g (renceng 20)' AS nama, 'Minuman' AS kat, '2000000000114' AS barcode, 22000 AS beli, 24500 AS jual, 1500 AS satuan_harga, 20 AS isi, 'ya' AS bisa, 160 AS stok, 'sachet' AS unit
    UNION ALL
    SELECT 'Neo Coffee 3in1 Mocca (renceng 9)' AS nama, 'Minuman' AS kat, '2000000000121' AS barcode, 11000 AS beli, 12500 AS jual, 1500 AS satuan_harga, 9 AS isi, 'ya' AS bisa, 90 AS stok, 'sachet' AS unit
    UNION ALL
    SELECT 'Indomie Goreng Jumbo 120g' AS nama, 'Makanan' AS kat, '2000000000138' AS barcode, 4400 AS beli, 5000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 60 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Indomie Ayam Bawang' AS nama, 'Makanan' AS kat, '2000000000145' AS barcode, 3100 AS beli, 3500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 100 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Indomie Kari Ayam' AS nama, 'Makanan' AS kat, '2000000000152' AS barcode, 3100 AS beli, 3500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 80 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Mie Sedaap Goreng' AS nama, 'Makanan' AS kat, '2000000000169' AS barcode, 3000 AS beli, 3500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 80 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Pop Mie Ayam Cup' AS nama, 'Makanan' AS kat, '2000000000176' AS barcode, 5300 AS beli, 6000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 36 AS stok, 'cup' AS unit
    UNION ALL
    SELECT 'Sarden ABC Saus Tomat 155g' AS nama, 'Makanan' AS kat, '2000000000183' AS barcode, 11800 AS beli, 13000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'kaleng' AS unit
    UNION ALL
    SELECT 'Kecap Manis Bango 700g' AS nama, 'Makanan' AS kat, '2000000000190' AS barcode, 25000 AS beli, 27500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 12 AS stok, 'pouch' AS unit
    UNION ALL
    SELECT 'Susu Kental Manis Indomilk 545g' AS nama, 'Makanan' AS kat, '2000000000206' AS barcode, 17300 AS beli, 18500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 18 AS stok, 'pouch' AS unit
    UNION ALL
    SELECT 'Susu Kental Manis Frisian Flag 535g' AS nama, 'Makanan' AS kat, '2000000000213' AS barcode, 19000 AS beli, 20500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 18 AS stok, 'pouch' AS unit
    UNION ALL
    SELECT 'Lays Rumput Laut 68g' AS nama, 'Snack' AS kat, '2000000000220' AS barcode, 9800 AS beli, 11000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Cheetos Jagung Bakar 60g' AS nama, 'Snack' AS kat, '2000000000237' AS barcode, 8900 AS beli, 10000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Taro Net Seaweed 32g' AS nama, 'Snack' AS kat, '2000000000244' AS barcode, 4800 AS beli, 5500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 36 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Oreo Vanilla 133g' AS nama, 'Snack' AS kat, '2000000000251' AS barcode, 8400 AS beli, 9500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Roma Kelapa 189g' AS nama, 'Snack' AS kat, '2000000000268' AS barcode, 8500 AS beli, 9500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Beng-Beng 20g' AS nama, 'Snack' AS kat, '2000000000275' AS barcode, 2100 AS beli, 2500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 100 AS stok, 'pcs' AS unit
    UNION ALL
    SELECT 'Beras Premium 5kg' AS nama, 'Sembako' AS kat, '2000000000282' AS barcode, 80000 AS beli, 84000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 20 AS stok, 'kantong' AS unit
    UNION ALL
    SELECT 'Beras Medium 5kg' AS nama, 'Sembako' AS kat, '2000000000299' AS barcode, 67000 AS beli, 70000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 20 AS stok, 'kantong' AS unit
    UNION ALL
    SELECT 'Beras Premium (per kg)' AS nama, 'Sembako' AS kat, '2000000000305' AS barcode, 16000 AS beli, 17000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 40 AS stok, 'kg' AS unit
    UNION ALL
    SELECT 'Beras Medium (per kg)' AS nama, 'Sembako' AS kat, '2000000000312' AS barcode, 13300 AS beli, 14500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 40 AS stok, 'kg' AS unit
    UNION ALL
    SELECT 'Minyakita 1 Liter' AS nama, 'Sembako' AS kat, '2000000000329' AS barcode, 15800 AS beli, 17000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 36 AS stok, 'liter' AS unit
    UNION ALL
    SELECT 'Minyak Goreng Bimoli Refill 2L' AS nama, 'Sembako' AS kat, '2000000000336' AS barcode, 42000 AS beli, 44500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 12 AS stok, 'pouch' AS unit
    UNION ALL
    SELECT 'Gula Pasir 1kg' AS nama, 'Sembako' AS kat, '2000000000343' AS barcode, 17600 AS beli, 18500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 30 AS stok, 'kg' AS unit
    UNION ALL
    SELECT 'Garam Dapur Halus 250g' AS nama, 'Sembako' AS kat, '2000000000350' AS barcode, 3900 AS beli, 4500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 36 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Tepung Terigu Segitiga Biru 1kg' AS nama, 'Sembako' AS kat, '2000000000367' AS barcode, 13000 AS beli, 14500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Telur Ayam Ras (per butir)' AS nama, 'Sembako' AS kat, '2000000000374' AS barcode, 1750 AS beli, 2000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 180 AS stok, 'butir' AS unit
    UNION ALL
    SELECT 'Telur Ayam Ras 1kg' AS nama, 'Sembako' AS kat, '2000000000381' AS barcode, 28500 AS beli, 31000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 20 AS stok, 'kg' AS unit
    UNION ALL
    SELECT 'Kapal Api Special Bubuk 250g' AS nama, 'Sembako' AS kat, '2000000000398' AS barcode, 37500 AS beli, 39500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 12 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Rokok Gudang Garam Filter 12' AS nama, 'Rokok' AS kat, '2000000000404' AS barcode, 26300 AS beli, 27500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 40 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Rokok Djarum Super 12' AS nama, 'Rokok' AS kat, '2000000000411' AS barcode, 26800 AS beli, 28500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 40 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Rokok Sampoerna Mild 16' AS nama, 'Rokok' AS kat, '2000000000428' AS barcode, 36000 AS beli, 37500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 40 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Rokok Marlboro Merah 20' AS nama, 'Rokok' AS kat, '2000000000435' AS barcode, 46000 AS beli, 48000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 30 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Sabun Mandi Lifebuoy 75g' AS nama, 'Perlengkapan Mandi' AS kat, '2000000000442' AS barcode, 3600 AS beli, 4500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 36 AS stok, 'pcs' AS unit
    UNION ALL
    SELECT 'Shampo Sunsilk 70ml' AS nama, 'Perlengkapan Mandi' AS kat, '2000000000459' AS barcode, 10200 AS beli, 11500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'botol' AS unit
    UNION ALL
    SELECT 'Pasta Gigi Pepsodent 75g' AS nama, 'Perlengkapan Mandi' AS kat, '2000000000466' AS barcode, 8000 AS beli, 9500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'tube' AS unit
    UNION ALL
    SELECT 'Sabun Cuci Piring Sunlight 400ml' AS nama, 'Rumah Tangga' AS kat, '2000000000473' AS barcode, 8800 AS beli, 10000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'pouch' AS unit
    UNION ALL
    SELECT 'Deterjen Rinso 770g' AS nama, 'Rumah Tangga' AS kat, '2000000000480' AS barcode, 21000 AS beli, 23500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 18 AS stok, 'bungkus' AS unit
    UNION ALL
    SELECT 'Korek Api Gas' AS nama, 'Rumah Tangga' AS kat, '2000000000497' AS barcode, 1800 AS beli, 2500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 60 AS stok, 'pcs' AS unit
    UNION ALL
    SELECT 'Baterai ABC AA (2 pcs)' AS nama, 'Rumah Tangga' AS kat, '2000000000503' AS barcode, 7200 AS beli, 8500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 24 AS stok, 'pack' AS unit
    UNION ALL
    SELECT 'Tolak Angin Cair Sachet' AS nama, 'Obat' AS kat, '2000000000510' AS barcode, 3800 AS beli, 4500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 60 AS stok, 'sachet' AS unit
    UNION ALL
    SELECT 'Antangin JRG Sachet' AS nama, 'Obat' AS kat, '2000000000527' AS barcode, 3400 AS beli, 4000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 60 AS stok, 'sachet' AS unit
    UNION ALL
    SELECT 'Bodrex Tablet (strip 4)' AS nama, 'Obat' AS kat, '2000000000534' AS barcode, 2000 AS beli, 2500 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 60 AS stok, 'strip' AS unit
    UNION ALL
    SELECT 'Minyak Kayu Putih Cap Lang 60ml' AS nama, 'Obat' AS kat, '2000000000541' AS barcode, 21000 AS beli, 24000 AS jual, 0 AS satuan_harga, 1 AS isi, 'tidak' AS bisa, 12 AS stok, 'botol' AS unit
) p
WHERE NOT EXISTS (
        SELECT 1 FROM dbwarungu.tb_barang b
        WHERE b.id_warung = w.id_warung
          AND ( CONVERT(LOWER(TRIM(b.nama_barang)) USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(LOWER(TRIM(p.nama)) USING utf8mb4) COLLATE utf8mb4_unicode_ci
                OR CONVERT(b.barcode USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(p.barcode USING utf8mb4) COLLATE utf8mb4_unicode_ci )
      )
  AND EXISTS (
        SELECT 1 FROM dbwarungu.tb_kategori c
        WHERE CONVERT(LOWER(TRIM(c.nama_kategori)) USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(LOWER(TRIM(p.kat)) USING utf8mb4) COLLATE utf8mb4_unicode_ci
      );
