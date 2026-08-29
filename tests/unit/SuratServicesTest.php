<?php

namespace Tests\Unit;

use App\Services\NomorSuratBuilderService;
use App\Services\NomorUrutService;
use CodeIgniter\Test\CIUnitTestCase;

class SuratServicesTest extends CIUnitTestCase
{
    public function testBuilderServiceFormatting()
    {
        $builder = new NomorSuratBuilderService();
        
        $number = $builder->build('025', 'PTA.KU', 'HM2.1.1', 'VIII', '2026');
        $this->assertEquals('025/PTA.KU/HM2.1.1/VIII/2026', $number);

        $number2 = $builder->build('100.a', 'INSTANSI-A', 'KEP', 'III', 2026);
        $this->assertEquals('100.a/INSTANSI-A/KEP/III/2026', $number2);
    }

    public function testRomanMonthConversion()
    {
        $this->assertEquals('I', NomorSuratBuilderService::getRomanMonth(1));
        $this->assertEquals('VIII', NomorSuratBuilderService::getRomanMonth(8));
        $this->assertEquals('XII', NomorSuratBuilderService::getRomanMonth(12));
    }

    public function testAlphaSuffixIncrement()
    {
        $this->assertEquals('a', NomorUrutService::getNextAlphaSuffix(null));
        $this->assertEquals('b', NomorUrutService::getNextAlphaSuffix('a'));
        $this->assertEquals('c', NomorUrutService::getNextAlphaSuffix('b'));
        $this->assertEquals('aa', NomorUrutService::getNextAlphaSuffix('z'));
    }

    public function testComponentValidation()
    {
        $builder = new NomorSuratBuilderService();

        // Valid data
        $validData = [
            'instansi'      => 'PTA.KU',
            'kode_surat'    => 'HM2.1.1',
            'bulan_romawi'  => 'VIII',
            'tahun_nomor'   => '2026',
            'tanggal_surat' => date('Y-m-d'),
            'perihal'       => 'Undangan Rapat',
            'tujuan'        => 'Kepala Dinas',
        ];
        $errors = $builder->validateComponents($validData);
        $this->assertEmpty($errors);

        // Invalid month
        $invalidData = $validData;
        $invalidData['bulan_romawi'] = 'XIII';
        $errors2 = $builder->validateComponents($invalidData);
        $this->assertArrayHasKey('bulan_romawi', $errors2);
    }
}
