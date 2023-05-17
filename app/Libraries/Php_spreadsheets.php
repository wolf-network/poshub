<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Php_spreadsheets {
    public function import_excel($file_path){
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);
        $spreadsheet = new Spreadsheet();
        $inputFileType = ucfirst($ext);
        $inputFileName = $file_path;
        /**  Create a new Reader of the type defined in $inputFileType  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        /**  Advise the Reader that we only want to load cell data  **/
        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);
        $worksheetData = $reader->listWorksheetInfo($inputFileName);
        $excel_upload_data = [];
        $spreadsheet_columns = [];
        $worksheet_name = [];
        foreach ($worksheetData as $worksheet) {
            $sheetName = $worksheet['worksheetName'];
            $finalized_excel_data[$sheetName] = [];
            /**  Load $inputFileName to a Spreadsheet Object  **/
            $reader->setLoadSheetsOnly($sheetName);
            $spreadsheet = $reader->load($inputFileName);

            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow         = $worksheet->getHighestDataRow(); // e.g. 10
            $highestColumn      = $worksheet->getHighestDataColumn(); // e.g 'F'
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 6
            $column_names_arr[$sheetName] = [];
            for($k=1;$k<=$highestColumnIndex;$k++){
                $headers = $worksheet->getCellByColumnAndRow($k,1)->getFormattedValue();
                if(!empty($headers)){
                    $spreadsheet_columns[$sheetName]['og'][] = trim($headers);
                    $spreadsheet_columns[$sheetName]['renamed'][] = str_replace(' ','',ucwords($headers));
                }
                
            }
            
            for($i=1;$i<$highestRow;$i++){
                for($j=1;$j<=$highestColumnIndex;$j++)
                {
                    $og_column_names = $worksheet->getCellByColumnAndRow($j,1)->getValue();
                    $column_names = str_replace(' ','',ucwords($og_column_names));
                    
                    if(!empty($column_names)){
                        array_push($column_names_arr[$sheetName],$column_names);
                    }

                    $column_values = $worksheet->getCellByColumnAndRow($j,($i+1))->getValue();
                    if(!empty($column_names)){
                        $excel_upload_data[$sheetName][$column_names][] = $column_values;
                    }
                }
            }
            
            if(!empty($excel_upload_data[$sheetName])){
                $worksheet_name[] = $sheetName;
                foreach($excel_upload_data[$sheetName] as $excel_column => $excel_data){
                    for($k=0;$k<($highestRow - 1);$k++){
                       $finalized_excel_data[$sheetName][$k][$excel_column] = strval($excel_data[$k]);
                    }
               }
            }else{
               unset($finalized_excel_data[$sheetName]); 
            }
        }
        
        return [
            'worksheet_name' => $worksheet_name,
            'headers' => $spreadsheet_columns,
            'data' => $finalized_excel_data
        ];
    }
    
    public function export_excel($headers = [], $data = [],$starting_cell = 0){
        $spreadsheet = new Spreadsheet();
        if(empty($data['sheets'])){
            $sheet = $spreadsheet->getActiveSheet();
            
            for($i=0;$i<count($headers);$i++){
                $starting_header = ($starting_cell != 0)?$starting_cell:'1';
                $column_alphabet = $this->getNameFromNumber($i);
                $sheet->setCellValue($column_alphabet.$starting_header, $headers[$i]);    
            }

            foreach($data as $data_key => $data_value){
                $j=0;
                foreach($data_value as $sub_data_key => $sub_data_value){
                    $starting_data = ($starting_cell != 0)?$starting_cell+$data_key+1:$data_key+2;
                    $data_alphabet = $this->getNameFromNumber($j);
                    $sheet->setCellValue($data_alphabet.($starting_data), $sub_data_value);
                    $j++;
                }
            }    
        }else{
            for($i=0;$i<count($data['sheets']);$i++){
               if($i != 0){   
                $spreadsheet->createSheet();
               }
               $spreadsheet->setActiveSheetIndex($i);
               $sheet = $spreadsheet->getActiveSheet($i);
               $spreadsheet->getActiveSheet($i)->setTitle($data['sheets'][$i]['sheet_title']);
                
               for($j=0;$j<count($data['sheets'][$i]['headers']);$j++){
                   $column_alphabet = $this->getNameFromNumber($j);
                   $sheet->setCellValue($column_alphabet.'1', $data['sheets'][$i]['headers'][$j]); 
               }
               
               $l = 0;
               foreach($data['sheets'][$i]['data'] as $data_key => $data_value){
                   if(is_array($data_value)){
                        $k=0;
                        foreach($data_value as $sub_data_key => $sub_data_value){
                            $data_alphabet = $this->getNameFromNumber($k);
                            $sheet->setCellValue($data_alphabet.($data_key+2), $sub_data_value);
                            $k++;
                        }   
                   }else{
                       $data_alphabet = $this->getNameFromNumber($l);
                       $sheet->setCellValue('A'.($l+2), $data_value);
                   }
                   
                   $l++;
               } 
            }
            $spreadsheet->setActiveSheetIndex(0);
        }
        
        $writer = new Xlsx($spreadsheet);

        $filename = date('Y-m-d H:i:s');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');

        $xlsxWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $xlsxWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        exit($xlsxWriter->save('php://output'));
    }
    
    private function getNameFromNumber($num) {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return $this->getNameFromNumber($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }
}