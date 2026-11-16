-- LANGKAH 3 dari 3: pemeriksaan jumlah barang per warung (hanya membaca, tidak mengubah data).
-- Tempel seluruh isi file ini ke SQL Editor TiDB, lalu klik Run.

SELECT w.id_warung, w.nama_warung, COUNT(b.id_barang) AS jumlah_barang
FROM dbwarungu.tb_warung w
LEFT JOIN dbwarungu.tb_barang b ON b.id_warung = w.id_warung
GROUP BY w.id_warung, w.nama_warung
ORDER BY w.id_warung;
