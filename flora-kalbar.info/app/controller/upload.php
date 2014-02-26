<?php
defined ('MICRODATA') or exit ( 'Forbidden Access' );

class upload extends Controller {
	
	var $models = FALSE;
	var $view;
	public function __construct()
	{
        global $basedomain;
		$this->loadmodule();
        $this->view = $this->setSmarty();
        $this->view->assign('basedomain',$basedomain);
	}
	public function loadmodule()
	{
		
		// $this->models = $this->loadModel('frontend');
	}
	
	public function index(){
        
		$var = array(1,2,3);

		$this->view->assign('test',$var);

		return $this->loadView('upload');

	}
    
    function zip(){
        global $CONFIG;
        
        //uploadFile --> engine function
        $name = 'imagezip';
        $path = '';
        
        if (!empty($username)){
            $uploaded_file = uploadFile($name, $path);
            if($uploaded_file['status'] != '0'){
                $path_extract = $uploaded_file['full_path'].$uploaded_file['raw_name'];
                $file = $uploaded_file['full_path'].$uploaded_file['full_name'];
                echo $path_extract;
                
                if($CONFIG['default']['unzip'] == 's_linux'){
                    s_linux_unzip($file, $path_extract);
                }elseif($CONFIG['default']['unzip'] == 'zipArchive'){
                    unzip($file, $path_extract);
                }
            }else{
                echo json_encode(array('status' => 'error', 'message' => $uploaded_file['message']));
            }
            pr($uploaded_file);
        }else{
            echo json_encode(array('status' => 'error', 'message' => 'Username must be filled'));
        }
    }
	
	function fetchExcel($sheet=1,$startRow=1,$startCol=0)
	{
		global $EXCEL;
		
			$data = array();
			$newData = array();
			
			$numberOfSheet = $sheet;
			$startRowData = $startRow;
			$startColData = $startCol;
			
			// parameternya adalah name dari input type file
			$excel = $this->excel('tes');
			
			if ($excel){
			
				for ($i=0; $i<$numberOfSheet; $i++){
					
					$data[$i]['sheet'] = $i;
					
					// get field name in current sheet
					$countColl = $excel->colcount($sheet_index=$i);
					$countRow = $excel->rowcount($sheet_index=$i);
					if ($countColl>0){
						for ($a=$startRowData; $a<=$countColl; $a++){
							$data[$i]['field_name'][] = $excel->val($startRowData, ($a), $i);
						}
					}
					
					if ($countRow>0){
						// looping baris
						for ($a=$startRowData; $a<=$countRow; $a++){
							
							// looping kolom
							
							for ($b=$startRowData; $b<=$countColl; $b++){
								
								$data[$i]['data'][$a][] = $excel->val($a+1, ($b), $i);
								
							}
							
						}
					}
					
				}
			}
			
			
			// clean data, if empty pass
			if ($data){
				foreach ($data as $key=>$val){
					
					
					foreach ($val['data'] as $keys=>$values){
						
						$newData[$key]['sheet'] = $val['sheet'];
						$newData[$key]['field_name'] = $val['field_name'];
						
						if (!empty($values[0])){
							
							$newData[$key]['data'][$keys] = $values;
						}
					}
				
				}
			}
			
			return $newData;
			
		
	}
	
	function parseExcel()
	{
		global $EXCEL;
		if ($_FILES){
			
			$numberOfSheet = 5;
			$startRowData = 1;
			$startColData = 1;
			
			$parseExcel = $this->fetchExcel($numberOfSheet,$startRowData,$startColData);
			
			pr($parseExcel);
		}
	}
}

?>