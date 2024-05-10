<?php namespace App\Libraries;

class HTMXDataTable
{
    protected $db;
    protected $table;
    protected $primaryKey;
    protected $session;

    public function __construct($db, $table, $primaryKey) {
        $this->db         = $db;
        $this->table      = $table;
        $this->primaryKey = $primaryKey;
        $this->session    = \Config\Services::session();
    }
    public function datatable($dt) {
        $request = \Config\Services::request();
        $newdata = [
                      'page'    => esc($request->getGet('page')) ?? $this->session->page,
                      'col_no'  => esc($request->getGet('col_no')) ?? $this->session->col_no,
                      'order'   => esc($request->getGet('order')) ?? $this->session->order,
                      'search'  => esc($request->getGet('search')),
                      'scol'    => esc($request->getGet('scol')),
                      'perPage' => 10,
        ];
        $this->session->set($newdata);
        array_unshift($dt['db_headers'], $this->table . '.' . $this->primaryKey);
        $builder = $this->db->table($this->table);
        $builder->select($dt['db_headers']);

        if (!empty($newdata['col_no'])) {
            $builder->orderBy($dt['db_headers'][$newdata['col_no']], $newdata['order']);
        }
        if ($newdata['search']) {
            $builder->like($dt['db_headers'][$newdata['scol']], $newdata['search'], 'both');
        }

        // Apply joins dynamically
        foreach ($dt['joins'] as $join) {
            $builder->join($join['table'], $join['condition'], $join['type']);
        }

        $query = $builder->get($newdata['perPage'], ($newdata['page'] - 1) * $newdata['perPage']);
        $data = [
            'data' => $query->getResultArray(),
            'totalRows' => $builder->countAllResults(false)
        ];

        return $data;
    }
    public function renderTable($dt,$data,$totalRows)
    {
        $html = '<table class="table table-hover">';
        $html .= '<thead class="text-primary h6"><tr>';
        $order = ($this->session->order=='asc' ? 'desc' : 'asc');
        $i=1;
        foreach ($dt['tbl_headers'] as $column) {
            $html .= '<th hx-get="'.base_url().'/'.$dt['route'].'?page=1&order='.$order.'&col_no='.$i.'" hx-trigger="click" hx-target="#datatable" hx-swap="outerHTML">' . $column . '</th>';
            $i++;
        }
        $html .= '</tr></thead>';

        $html .= '<tbody>';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . $cell . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        return $this->populateTable($html,$this->renderPagination($dt,$totalRows));
    }

    public function renderPagination($dt,$totalRows)
    {
        // Assuming $totalRows, $perPage, and $route are defined elsewhere in your code
        $totalPages = ceil($totalRows / $this->session->perPage);
        $currentPage = $this->session->page;
        // Calculate the range of pages to display
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);
        $html = '<div class="pagination">';
        // First button
        $html .= '<a class="btn btn-sm rounded-pill btn-outline-primary me-3" href="#" hx-get="'.base_url().'/'.$dt['route'].'?page=1" hx-trigger="click" hx-target="#datatable" hx-swap="outerHTML">First</a> ';
        // Previous button
        if ($currentPage > 1) {
            $html .= '<a class="btn btn-sm rounded-pill btn-outline-primary me-3" href="#" hx-get="'.base_url().'/'.$dt['route'].'?page=' . ($currentPage - 1) . '" hx-trigger="click" hx-target="#datatable" hx-swap="outerHTML">Previous</a> ';
        }
        // Display page numbers
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $currentPage) {
                $html .= '<a class="btn btn-sm rounded-pill btn-outline-primary me-3 active" href="#" hx-get="'.base_url().'/'.$dt['route'].'?page=' . $i . '" hx-trigger="click" hx-target="#datatable" hx-swap="outerHTML">' . $i . '</a> ';
            } else {
                $html .= '<a class="btn btn-sm rounded-pill btn-outline-primary me-3" href="#" hx-get="'.base_url().'/'.$dt['route'].'?page=' . $i . '" hx-trigger="click" hx-target="#datatable" hx-swap="outerHTML">' . $i . '</a> ';
            }
        }
        // Next button
        if ($currentPage < $totalPages) {
            $html .= '<a class="btn btn-sm rounded-pill btn-outline-primary me-3" href="#" hx-get="'.base_url().'/'.$dt['route'].'?page=' . ($currentPage + 1) . '" hx-trigger="click" hx-target="#datatable" hx-swap="outerHTML">Next</a> ';
        }
        // Last button
        $html .= '<a class="btn btn-sm rounded-pill btn-outline-primary me-3" href="#" hx-get="'.base_url().'/'.$dt['route'].'?page=' . $totalPages . '" hx-trigger="click" hx-target="#datatable" hx-swap="outerHTML">Last</a> ';

        $html .= '</div>';
        return $html;
    }
     public function renderFilters($dt)
    {
        $addnew = $dt['route']."/addnew/";
        $print = $dt['route']."/printall/";
        $header = "<div class='row'>
                      <div class='col-3 me-auto'>
                        <div class='btn-toolbar mb-2 mb-md-0'>
                          <a class='btn btn-outline-success' href='".base_url()."".$addnew."'>
                           <i class='bi bi-plus-circle-fill'></i>
                            Add New
                          </a>
                          <a class='btn ms-2 btn-outline-secondary' href='".base_url()."/".$print."'>
                            <i class='bi bi-printer'></i>
                            Print All
                          </a>
                        </div>
                      </div>
                      <div class='col-2'>
                        <select name='scol' class='form-control' 
                            hx-get='".base_url()."".$dt['route']."' 
                            hx-target='#datatable' 
                            hx-indicator='.htmx-indicator'
                            hx-include='[name=\"search\"]'
                            hx-trigger='input changed delay:500ms, search' >
                            <option value=''>Search by...</option>";
                            $i=1;
                            foreach($dt['filter_options'] as $th){
                                $header .="<option value='".$i++."'>".$th."</option>";
                            }                           
                        $header .="</select>
                      </div>
                      <div class='col-3'>
                        <input class='form-control col-3' type='search' 
                           name='search' placeholder='Begin Typing To Search data...' 
                           hx-get='".base_url()."".$dt['route']."' 
                           hx-target='#datatable' 
                           hx-indicator='.htmx-indicator'
                           hx-include='[name=\"scol\"]'
                           hx-trigger='input changed delay:500ms, search' >
                      </div>
                   </div>";

        return $header;
    }
    public function populateTable($html,$pagination)
    {
        return "<div id='datatable' class='table-responsive'>".$html."<br>".$pagination."</div>";
    }
}
