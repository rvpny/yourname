<?php
class peminjaman_model {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }    

    public function pinjamBuku($data) {
        $query = "INSERT INTO peminjaman 
                 (id_user, id_buku, tanggal_pinjam, tanggal_harus_kembali, status) 
                 VALUES (:id_user, :id_buku, :tanggal_pinjam, :tanggal_harus_kembali, 'dipinjam')";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }
    
    public function kembalikanBuku($id_peminjaman) {
        $query = "UPDATE peminjaman 
                 SET tanggal_kembali = CURDATE(), 
                     status = IF(tanggal_harus_kembali < CURDATE(), 'terlambat', 'dikembalikan'),    
                 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id_peminjaman]);
    }
    
    public function getPeminjamanAktif($id_user) {
        $query = "SELECT p.*, b.judul, b.cover 
                 FROM peminjaman p
                 
                 JOIN buku b ON p.id_buku = b.id
                 WHERE p.id_user = :id_user AND p.status = 'dipinjam'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id_user' => $id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
?>