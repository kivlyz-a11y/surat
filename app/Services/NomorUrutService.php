<?php

namespace App\Services;

use App\Models\NomorSuratCounterModel;
use App\Models\SettingModel;
use App\Models\SuratModel;
use Config\Database;

class NomorUrutService
{
    protected $db;
    protected SettingModel $settingModel;
    protected SuratModel $suratModel;
    protected NomorSuratCounterModel $counterModel;

    public function __construct()
    {
        $this->db           = Database::connect();
        $this->settingModel = new SettingModel();
        $this->suratModel   = new SuratModel();
        $this->counterModel = new NomorSuratCounterModel();
    }

    /**
     * Generate the next alphabetical suffix: a, b, c, ... z, aa, ab, ...
     */
    public static function getNextAlphaSuffix(?string $currentSuffix = null): string
    {
        if ($currentSuffix === null || $currentSuffix === '') {
            return 'a';
        }
        return ++$currentSuffix;
    }

    /**
     * Generate next nomor urut with database locking and backdate handling
     *
     * @param string $tanggalSurat (Y-m-d)
     * @param int $tahunNomor
     * @return array [
     *    'nomor_urut'   => string (e.g. '026' or '100.a'),
     *    'is_backdate'  => int (0 or 1),
     *    'base_number'  => string,
     *    'suffix'       => string|null
     * ]
     * @throws \Exception
     */
    public function generateNomorUrut(string $tanggalSurat, int $tahunNomor): array
    {
        $settings     = $this->settingModel->getSettings();
        $paddingDigit = (int)($settings['padding_digit'] ?? 3);
        $modeCounter  = $settings['mode_counter'] ?? 'global';
        $today        = date('Y-m-d');

        // Check if backdate: date is strictly less than today
        $isBackdateRequest = ($tanggalSurat < $today);

        if ($isBackdateRequest) {
            // 1. BACKDATE CASE:
            // Check if there are letters created for this date or newer dates in the database
            $newerSurat = $this->db->table('surat')
                ->where('tanggal_surat >', $tanggalSurat)
                ->where('status !=', 'Dibatalkan')
                ->countAllResults();

            $sameDateSurat = $this->db->table('surat')
                ->where('tanggal_surat', $tanggalSurat)
                ->where('status !=', 'Dibatalkan')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();

            if ($newerSurat > 0 || !empty($sameDateSurat)) {
                // Determine base letter for this backdate
                if (!empty($sameDateSurat)) {
                    // There are already letters on this date
                    // Find all suffixes for this date
                    $baseSurat = $sameDateSurat[0];
                    $rawNomorUrut = $baseSurat['nomor_urut'];

                    // Extract base number without suffix (e.g. '100' from '100' or '100.a')
                    $parts = explode('.', $rawNomorUrut);
                    $baseNumber = $parts[0];

                    // Find all existing letters with this base number in the same year/date
                    $existingWithBase = $this->db->table('surat')
                        ->where('tahun_nomor', $tahunNomor)
                        ->like('nomor_urut', $baseNumber, 'after')
                        ->get()
                        ->getResultArray();

                    $suffixes = [];
                    foreach ($existingWithBase as $s) {
                        $sParts = explode('.', $s['nomor_urut']);
                        if ($sParts[0] === $baseNumber) {
                            if (isset($sParts[1])) {
                                $suffixes[] = strtolower($sParts[1]);
                            }
                        }
                    }

                    // Suffix sequence
                    if (empty($suffixes)) {
                        $nextSuffix = 'a';
                    } else {
                        // Sort suffixes
                        usort($suffixes, function($a, $b) {
                            if (strlen($a) === strlen($b)) {
                                return strcmp($a, $b);
                            }
                            return strlen($a) - strlen($b);
                        });
                        $lastSuffix = end($suffixes);
                        $nextSuffix = self::getNextAlphaSuffix($lastSuffix);
                    }

                    $nomorUrutFinal = "{$baseNumber}.{$nextSuffix}";

                    return [
                        'nomor_urut'   => $nomorUrutFinal,
                        'is_backdate'  => 1,
                        'base_number'  => $baseNumber,
                        'suffix'       => $nextSuffix,
                    ];
                } else {
                    // No letter on this exact past date, find latest letter before this date
                    $prevSurat = $this->db->table('surat')
                        ->where('tanggal_surat <', $tanggalSurat)
                        ->where('status !=', 'Dibatalkan')
                        ->orderBy('tanggal_surat', 'DESC')
                        ->orderBy('id', 'DESC')
                        ->get()
                        ->getFirstRow('array');

                    if ($prevSurat) {
                        $parts = explode('.', $prevSurat['nomor_urut']);
                        $baseNumber = $parts[0];

                        // Find existing suffixes
                        $existingWithBase = $this->db->table('surat')
                            ->where('tahun_nomor', $tahunNomor)
                            ->like('nomor_urut', $baseNumber, 'after')
                            ->get()
                            ->getResultArray();

                        $suffixes = [];
                        foreach ($existingWithBase as $s) {
                            $sParts = explode('.', $s['nomor_urut']);
                            if ($sParts[0] === $baseNumber && isset($sParts[1])) {
                                $suffixes[] = strtolower($sParts[1]);
                            }
                        }

                        if (empty($suffixes)) {
                            $nextSuffix = 'a';
                        } else {
                            usort($suffixes, function($a, $b) {
                                if (strlen($a) === strlen($b)) {
                                    return strcmp($a, $b);
                                }
                                return strlen($a) - strlen($b);
                            });
                            $lastSuffix = end($suffixes);
                            $nextSuffix = self::getNextAlphaSuffix($lastSuffix);
                        }

                        $nomorUrutFinal = "{$baseNumber}.{$nextSuffix}";

                        return [
                            'nomor_urut'   => $nomorUrutFinal,
                            'is_backdate'  => 1,
                            'base_number'  => $baseNumber,
                            'suffix'       => $nextSuffix,
                        ];
                    }
                }
            }
        }

        // 2. STANDARD SEQUENTIAL CASE:
        // Use Database locking: SELECT ... FOR UPDATE
        $tahunCounterKey = ($modeCounter === 'per_tahun') ? $tahunNomor : 0;

        // Fetch counter record with FOR UPDATE lock
        $counterRow = $this->db->query(
            "SELECT id, tahun_counter, nomor_terakhir FROM nomor_surat_counters WHERE tahun_counter = ? FOR UPDATE",
            [$tahunCounterKey]
        )->getFirstRow('array');

        if (!$counterRow) {
            // Also check if any existing surat max number
            $maxSuratNum = 0;
            $existingMax = $this->db->table('surat')
                ->select('nomor_urut')
                ->orderBy('id', 'DESC')
                ->get()
                ->getFirstRow('array');

            if ($existingMax) {
                $p = explode('.', $existingMax['nomor_urut']);
                $maxSuratNum = (int)$p[0];
            }

            $this->db->table('nomor_surat_counters')->insert([
                'tahun_counter'  => $tahunCounterKey,
                'nomor_terakhir' => $maxSuratNum,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            $counterRow = $this->db->query(
                "SELECT id, tahun_counter, nomor_terakhir FROM nomor_surat_counters WHERE tahun_counter = ? FOR UPDATE",
                [$tahunCounterKey]
            )->getFirstRow('array');
        }

        $nextNumber = (int)$counterRow['nomor_terakhir'] + 1;

        // Update counter immediately within transaction
        $this->db->table('nomor_surat_counters')
            ->where('id', $counterRow['id'])
            ->update([
                'nomor_terakhir' => $nextNumber,
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

        // Format with zero padding (e.g. 001, 025, 100)
        $nomorUrutFinal = str_pad((string)$nextNumber, $paddingDigit, '0', STR_PAD_LEFT);

        return [
            'nomor_urut'   => $nomorUrutFinal,
            'is_backdate'  => 0,
            'base_number'  => $nomorUrutFinal,
            'suffix'       => null,
        ];
    }

    /**
     * Preview what the next nomor urut will look like without incrementing
     */
    public function previewNextNomorUrut(string $tanggalSurat, int $tahunNomor): array
    {
        $settings     = $this->settingModel->getSettings();
        $paddingDigit = (int)($settings['padding_digit'] ?? 3);
        $modeCounter  = $settings['mode_counter'] ?? 'global';
        $today        = date('Y-m-d');

        $isBackdateRequest = ($tanggalSurat < $today);

        if ($isBackdateRequest) {
            $newerSurat = $this->db->table('surat')
                ->where('tanggal_surat >', $tanggalSurat)
                ->where('status !=', 'Dibatalkan')
                ->countAllResults();

            $sameDateSurat = $this->db->table('surat')
                ->where('tanggal_surat', $tanggalSurat)
                ->where('status !=', 'Dibatalkan')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();

            if ($newerSurat > 0 || !empty($sameDateSurat)) {
                if (!empty($sameDateSurat)) {
                    $baseSurat = $sameDateSurat[0];
                    $parts = explode('.', $baseSurat['nomor_urut']);
                    $baseNumber = $parts[0];

                    $existingWithBase = $this->db->table('surat')
                        ->where('tahun_nomor', $tahunNomor)
                        ->like('nomor_urut', $baseNumber, 'after')
                        ->get()
                        ->getResultArray();

                    $suffixes = [];
                    foreach ($existingWithBase as $s) {
                        $sParts = explode('.', $s['nomor_urut']);
                        if ($sParts[0] === $baseNumber && isset($sParts[1])) {
                            $suffixes[] = strtolower($sParts[1]);
                        }
                    }

                    $nextSuffix = empty($suffixes) ? 'a' : self::getNextAlphaSuffix(end($suffixes));
                    return [
                        'nomor_urut'  => "{$baseNumber}.{$nextSuffix}",
                        'is_backdate' => 1,
                        'message'     => "Tanggal mundur terdeteksi. Nomor urut akan menggunakan suffix alfabet: {$baseNumber}.{$nextSuffix}",
                    ];
                }
            }
        }

        $tahunCounterKey = ($modeCounter === 'per_tahun') ? $tahunNomor : 0;
        $counterRow = $this->db->table('nomor_surat_counters')
            ->where('tahun_counter', $tahunCounterKey)
            ->get()
            ->getFirstRow('array');

        $currentNumber = $counterRow ? (int)$counterRow['nomor_terakhir'] : 0;
        $nextNumber    = $currentNumber + 1;
        $nomorUrutFinal = str_pad((string)$nextNumber, $paddingDigit, '0', STR_PAD_LEFT);

        return [
            'nomor_urut'  => $nomorUrutFinal,
            'is_backdate' => 0,
            'message'     => "Nomor urut berikutnya: {$nomorUrutFinal}",
        ];
    }

    /**
     * Get the latest issued letter number info
     */
    public function getLatestNomorInfo(): ?array
    {
        return $this->db->table('surat')
            ->orderBy('id', 'DESC')
            ->get()
            ->getFirstRow('array');
    }
}
