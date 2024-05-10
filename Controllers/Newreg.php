<?php
namespace App\Controllers;
use App\Models\StuModel;
use App\Libraries\HTMXDataTable;

class Newreg extends BaseController {
	protected $datatable;
	protected $db;
	protected $dt;
	public function __construct() {
		 $this->db = \Config\Database::connect();
	     $this->datatable = new HTMXDataTable($this->db, 'table_name', 'primary_key');
	     $this->dt['route'] = '/newreg/datatable';
	     $this->dt['filter_options'] = ['Column 1', 'Column 2e', 'Column 3', 'Column 4', 'Column 5', 'Column 6'];
	     $this->dt['db_headers']  = ['column_1', 'column_2', 'column_3', 'column_4', 'column_5','column_6'];
	     $this->dt['tbl_headers'] = ['Column 1', 'Column 2e', 'Column 3', 'Column 4', 'Column 5', 'Column 6','Action'];
	     $this->dt['joins'] = [
	            [
	                'table'     => 'table2 t2',
	                'condition' => 'table1.table2_colume=t2.id',
	                'type'      => 'left'
	            ],
	        ];
  }
  public function index() {
    $data['filters']  = $this->datatable->renderFilters($this->dt);
    echo view('shared/nav');
    echo view('stuetrregadd',$data);
    echo view('shared/footer');
  }
  public function datatable()
   {
        $dtdata = $this->datatable->datatable($this->dt);
        foreach($dtdata['data'] as &$d){
           $d['text'] = "<a class='btn btn-sm rounded-pill btn-outline-primary' href='' >Edit</a>&nbsp;&nbsp;
            <a class='btn btn-sm rounded-pill btn-outline-danger' href='".$d['id']."'>Delete</a>";
           array_shift($d);
        }
        $html = $this->datatable->renderTable($this->dt,$dtdata['data'],$dtdata['totalRows']);

        return $html;
   }
	public function stuprereg() {
		$model = new StuModel();
	    $q  = $this->request->getVar('q');
	    $sr = $this->request->getVar('sr');
	    if(!empty($q)){
	      $data['data']   = $model->like('studnamee',$q)->orderBy('id', 'DESC')->paginate(10);
	      $data['pager']  = $model->pager;
	      $data['sortDir']= "asc";
	    }else{
			  $data['data']   = $model->orderBy('id', 'DESC')->paginate(10);
	      $data['pager']  = $model->pager;
	      $data['sortDir']= "asc";
	    }
			echo view('studata',$data);
	}
}
