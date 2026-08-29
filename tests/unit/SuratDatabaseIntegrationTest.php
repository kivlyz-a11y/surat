<?php

namespace Tests\Unit;

use App\Models\SuratModel;
use App\Services\NomorSuratBuilderService;
use App\Services\NomorUrutService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Database;

class SuratDatabaseIntegrationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refresh = false;

    public function testSequentialAndBackdateWorkflow()
    {
        $service = new NomorUrutService();
        $builder = new NomorSuratBuilderService();
        $db      = Database::connect();

        $db->transBegin();

        // 1. Sequential Generation
        $seq = $service->generateNomorUrut(date('Y-m-d'), (int)date('Y'));
        $this->assertEquals(0, $seq['is_backdate']);
        $this->assertNotEmpty($seq['nomor_urut']);

        $builtNumber = $builder->build($seq['nomor_urut'], 'PTA.KU', 'HM2.1.1', 'VIII', 2026);
        $this->assertStringContainsString($seq['nomor_urut'], $builtNumber);

        // 2. Backdate Generation for 2026-01-15 (Base was 001 in seed)
        $back1 = $service->generateNomorUrut('2026-01-15', 2026);
        $this->assertEquals(1, $back1['is_backdate']);
        $this->assertEquals('001.a', $back1['nomor_urut']);

        // Insert temp surat 001.a
        $db->table('surat')->insert([
            'nomor_urut'    => $back1['nomor_urut'],
            'nomor_surat'   => "001.a/PTA.KU/HM2.1.1/I/2026",
            'instansi'      => 'PTA.KU',
            'kode_surat'    => 'HM2.1.1',
            'bulan_romawi'  => 'I',
            'tahun_nomor'   => 2026,
            'tanggal_surat' => '2026-01-15',
            'perihal'       => 'Test Surat Backdate Suffix',
            'tujuan'        => 'Test',
            'nama_pembuat'  => 'Test User',
            'status'        => 'Nomor Diambil',
            'is_backdate'   => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        // Next Backdate on same date must be 001.b
        $back2 = $service->generateNomorUrut('2026-01-15', 2026);
        $this->assertEquals(1, $back2['is_backdate']);
        $this->assertEquals('001.b', $back2['nomor_urut']);

        // Rollback test
        $db->transRollback();
    }
}
