-- LANGKAH 1 dari 3: tambah kategori yang belum ada (aman dijalankan berulang kali).
-- Tempel seluruh isi file ini ke SQL Editor TiDB, lalu klik Run.

INSERT INTO dbwarungu.tb_kategori (nama_kategori, created_at, updated_at)
SELECT k.nama, NOW(), NOW()
FROM (
    SELECT 'Minuman' AS nama
    UNION ALL
    SELECT 'Makanan' AS nama
    UNION ALL
    SELECT 'Snack' AS nama
    UNION ALL
    SELECT 'Sembako' AS nama
    UNION ALL
    SELECT 'Rokok' AS nama
    UNION ALL
    SELECT 'Obat' AS nama
    UNION ALL
    SELECT 'Perlengkapan Mandi' AS nama
    UNION ALL
    SELECT 'Rumah Tangga' AS nama
) k
WHERE NOT EXISTS (
    SELECT 1 FROM dbwarungu.tb_kategori c
    WHERE CONVERT(LOWER(TRIM(c.nama_kategori)) USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(LOWER(TRIM(k.nama)) USING utf8mb4) COLLATE utf8mb4_unicode_ci
);
