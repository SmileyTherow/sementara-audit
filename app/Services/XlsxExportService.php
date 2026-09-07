<?php

namespace App\Services;

use ZipArchive;

class XlsxExportService
{
    public function download(array $report)
    {
        $rows = [['Laporan Panen KWT Kota Tangerang'], ['Periode', $report['start'], $report['end']], ['Ekspektasi/proyeksi penuh siklus; realisasi hanya dalam periode.'], ['KWT', 'Komoditas', 'Kode', 'Bibit', 'Ekspektasi', 'Proyeksi', 'Realisasi', 'Selisih', 'Capaian (%)', 'Satuan']];
        foreach ($report['groupedByKwt'] as $kwt => $items) {
            foreach ($items as $r) {
                $rows[] = [$r['kwt'], $r['commodity'], $r['code'], $r['seed'], $r['expected'], $r['projected'], $r['actual'], $r['variance'], $r['achievement'] ?? '', $r['unit']];
            }
            foreach ($report['subtotal'][$kwt] as $unit => $totals) {
                $rows[] = ['Subtotal '.$kwt, '', '', '', $totals['expected'], $totals['projected'], $totals['actual'], '', '', $unit];
            }
        }
        foreach ($report['totalsByUnit'] as $unit => $totals) {
            $rows[] = ['Total', '', '', '', $totals['expected'], $totals['projected'], $totals['actual'], '', '', $unit];
        }
        $rows[] = ['Rekap per komoditas', 'Jumlah KWT', 'Satuan', 'Ekspektasi', 'Proyeksi', 'Realisasi'];
        foreach ($report['groupedByCommodity'] as $name => $group) {
            foreach ($group['totals'] as $unit => $total) {
                $rows[] = [$name, $group['kwts'], $unit, $total['expected'], $total['projected'], $total['actual']];
            }
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
        foreach ($rows as $i => $row) {
            $xml .= '<row r="'.($i + 1).'">';
            foreach ($row as $j => $value) {
                $ref = chr(65 + $j).($i + 1);
                $xml .= is_int($value) || is_float($value) ? '<c r="'.$ref.'"><v>'.$value.'</v></c>' : '<c r="'.$ref.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</t></is></c>';
            } $xml .= '</row>';
        }
        $xml .= '</sheetData></worksheet>';
        $path = tempnam(sys_get_temp_dir(), 'kwt-xlsx-');
        $zip = new ZipArchive;
        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat XLSX.');
        }
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Laporan Panen" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
        $zip->addFromString('xl/worksheets/sheet1.xml', $xml);
        $zip->close();

        return response()->download($path, 'laporan-panen-'.$report['start'].'.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])->deleteFileAfterSend();
    }
}
