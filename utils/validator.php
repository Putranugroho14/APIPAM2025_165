<?php

/**
 * Validator Class - Central validation for all inputs
 */
class Validator
{

    /**
     * Validasi nama (hanya huruf, spasi, dan titik)
     */
    public static function validateNama($nama, $min = 3, $max = 100)
    {
        if (empty($nama)) {
            return ["valid" => false, "message" => "Nama tidak boleh kosong"];
        }

        $nama = trim($nama);
        $length = mb_strlen($nama);

        if ($length < $min) {
            return ["valid" => false, "message" => "Nama minimal $min karakter"];
        }

        if ($length > $max) {
            return ["valid" => false, "message" => "Nama maksimal $max karakter"];
        }

        if (!preg_match("/^[a-zA-Z .]+$/u", $nama)) {
            return ["valid" => false, "message" => "Nama hanya boleh berisi huruf, spasi, dan titik"];
        }

        return ["valid" => true, "value" => $nama];
    }

    /**
     * Validasi nomor HP Indonesia (10-15 digit, diawali 0)
     */
    public static function validateNoHP($no_hp)
    {
        if (empty($no_hp)) {
            return ["valid" => false, "message" => "Nomor HP tidak boleh kosong"];
        }

        $no_hp = preg_replace("/[^0-9]/", "", $no_hp);

        if (strlen($no_hp) < 10) {
            return ["valid" => false, "message" => "Nomor HP minimal 10 digit"];
        }

        if (strlen($no_hp) > 15) {
            return ["valid" => false, "message" => "Nomor HP maksimal 15 digit"];
        }

        if (!preg_match("/^0[0-9]{9,14}$/", $no_hp)) {
            return ["valid" => false, "message" => "Nomor HP harus diawali angka 0"];
        }

        return ["valid" => true, "value" => $no_hp];
    }

    /**
     * Validasi plat nomor Indonesia
     */
    public static function validatePlatNomor($plat)
    {
        if (empty($plat)) {
            return ["valid" => false, "message" => "Plat nomor tidak boleh kosong"];
        }

        $plat = strtoupper(trim($plat));

        if (!preg_match("/^[A-Z]{1,2} [0-9]{1,4} [A-Z]{1,3}$/", $plat)) {
            return ["valid" => false, "message" => "Format plat tidak valid (Contoh: B 1234 XYZ)"];
        }

        return ["valid" => true, "value" => $plat];
    }

    /**
     * Validasi merek mobil
     */
    public static function validateMerek($merek, $min = 2, $max = 50)
    {
        if (empty($merek)) {
            return ["valid" => false, "message" => "Merek tidak boleh kosong"];
        }

        $merek = trim($merek);
        $length = mb_strlen($merek);

        if ($length < $min) {
            return ["valid" => false, "message" => "Merek minimal $min karakter"];
        }

        if ($length > $max) {
            return ["valid" => false, "message" => "Merek maksimal $max karakter"];
        }

        if (!preg_match("/^[a-zA-Z0-9 -]+$/", $merek)) {
            return ["valid" => false, "message" => "Merek hanya boleh berisi huruf, angka, spasi, dan dash (-)"];
        }

        return ["valid" => true, "value" => $merek];
    }

    /**
     * PERBAIKAN: Validasi biaya (Minimal Rp 10.000 agar tidak bisa input Rp 4)
     */
    public static function validateBiaya($biaya, $min_biaya = 10000)
    {
        if (!is_numeric($biaya)) {
            return ["valid" => false, "message" => "Biaya harus berupa angka"];
        }

        $biaya = floatval($biaya);

        if ($biaya < $min_biaya) {
            // Memberikan pesan error spesifik agar user tahu batas minimal
            return ["valid" => false, "message" => "Biaya servis minimal Rp " . number_format($min_biaya, 0, ',', '.')];
        }

        return ["valid" => true, "value" => $biaya];
    }

    /**
     * Validasi deskripsi (minimal 5 karakter)
     */
    public static function validateDeskripsi($deskripsi, $min = 5)
    {
        if (empty($deskripsi)) {
            return ["valid" => false, "message" => "Deskripsi tidak boleh kosong"];
        }

        $deskripsi = trim($deskripsi);

        if (mb_strlen($deskripsi) < $min) {
            return ["valid" => false, "message" => "Deskripsi minimal $min karakter"];
        }

        return ["valid" => true, "value" => $deskripsi];
    }

    /**
     * Validasi status servis
     */
    public static function validateStatus($status)
    {
        $valid_status = ['proses', 'selesai', 'batal'];
        $status = strtolower(trim($status));

        if (!in_array($status, $valid_status)) {
            return ["valid" => false, "message" => "Status harus: proses, selesai, atau batal"];
        }

        return ["valid" => true, "value" => $status];
    }

    public static function validateTanggalServis($tanggal)
    {
        if (empty($tanggal)) {
            return ['valid' => false, 'message' => 'Tanggal servis harus diisi'];
        }

        $date = DateTime::createFromFormat('Y-m-d', $tanggal);
        if (!$date || $date->format('Y-m-d') !== $tanggal) {
            return ['valid' => false, 'message' => 'Format tanggal tidak valid (YYYY-MM-DD)'];
        }

        // ✅ PERBAIKAN: Gunakan timezone yang sama atau toleransi 1 hari
        $today = new DateTime('now', new DateTimeZone('Asia/Jakarta')); // Sesuaikan timezone
        $today->setTime(0, 0, 0); // Reset ke 00:00:00

        $inputDate = clone $date;
        $inputDate->setTime(0, 0, 0);

        // Toleransi: tanggal tidak boleh lebih dari 1 hari ke depan
        $tomorrow = clone $today;
        $tomorrow->modify('+1 day');

        if ($inputDate > $tomorrow) {
            return ['valid' => false, 'message' => 'Tanggal tidak boleh di masa depan'];
        }

        return ['valid' => true, 'value' => $date->format('Y-m-d')];
    }
}
