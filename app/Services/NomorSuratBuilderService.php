<?php

namespace App\Services;

class NomorSuratBuilderService
{
    /**
     * Roman months mapping
     */
    public static array $romanMonths = [
        1  => 'I',
        2  => 'II',
        3  => 'III',
        4  => 'IV',
        5  => 'V',
        6  => 'VI',
        7  => 'VII',
        8  => 'VIII',
        9  => 'IX',
        10 => 'X',
        11 => 'XI',
        12 => 'XII',
    ];

    /**
     * Convert month number (1-12) to Roman numeral
     */
    public static function getRomanMonth(int $month): string
    {
        return self::$romanMonths[$month] ?? 'I';
    }

    /**
     * Build full nomor_surat string
     * Format: {nomor_urut}/{instansi}/{kode_surat}/{bulan_romawi}/{tahun}
     */
    public function build(
        string $nomorUrut,
        string $instansi,
        string $kodeSurat,
        string $bulanRomawi,
        int|string $tahunNomor,
        string $customFormat = '{nomor_urut}/{instansi}/{kode_surat}/{bulan_romawi}/{tahun}'
    ): string {
        $nomorUrut   = trim($nomorUrut);
        $instansi    = trim($instansi);
        $kodeSurat   = trim($kodeSurat);
        $bulanRomawi = strtoupper(trim($bulanRomawi));
        $tahunNomor  = trim((string)$tahunNomor);

        // Replace template placeholders if custom format provided
        $placeholders = [
            '{nomor_urut}'    => $nomorUrut,
            '{instansi}'      => $instansi,
            '{kode_surat}'    => $kodeSurat,
            '{bulan_romawi}'  => $bulanRomawi,
            '{tahun}'         => $tahunNomor,
            '{tahun_nomor}'   => $tahunNomor,
        ];

        return strtr($customFormat, $placeholders);
    }

    /**
     * Validate individual components
     */
    public function validateComponents(array $data): array
    {
        $errors = [];

        if (empty($data['instansi'])) {
            $errors['instansi'] = 'Instansi wajib diisi.';
        }

        if (empty($data['kode_surat'])) {
            $errors['kode_surat'] = 'Kode surat wajib diisi.';
        }

        $validRomawi = array_values(self::$romanMonths);
        if (empty($data['bulan_romawi']) || !in_array(strtoupper(trim($data['bulan_romawi'])), $validRomawi)) {
            $errors['bulan_romawi'] = 'Bulan Romawi harus berupa angka Romawi I sampai XII.';
        }

        if (empty($data['tahun_nomor']) || !preg_match('/^\d{4}$/', (string)$data['tahun_nomor'])) {
            $errors['tahun_nomor'] = 'Tahun harus berupa 4 digit angka (misal: ' . date('Y') . ').';
        }

        if (empty($data['tanggal_surat'])) {
            $errors['tanggal_surat'] = 'Tanggal surat wajib diisi.';
        } else {
            $today = date('Y-m-d');
            if ($data['tanggal_surat'] > $today) {
                $errors['tanggal_surat'] = 'Tanggal surat tidak boleh melebihi tanggal hari ini (' . date('d/m/Y') . ').';
            }
        }

        if (empty($data['perihal'])) {
            $errors['perihal'] = 'Perihal surat wajib diisi.';
        }

        if (empty($data['tujuan'])) {
            $errors['tujuan'] = 'Tujuan surat wajib diisi.';
        }

        return $errors;
    }
}
