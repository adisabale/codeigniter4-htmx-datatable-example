<?php
      $sortDir = ($sortDir=== 'asc' ? 'desc' : 'asc');  
?>
<table class="table table-hover table-sm">
    <thead>
      <tr>
        <th>#</th>
        <th hx-get="stuprereg"
            hx-trigger='click'
            hx-replace-url='true'
            hx-swap='outerHTML'
            hx-target='#search-results' name="sort" hx-params="">>Colume 1</th>
        <th>Colume 2</th>
        <th>Colume 3</th>
        <th>Colume 4</th>
        <th>Action</th>
      </tr>
    </thead>
<tbody >
<?php
$id = 1;
foreach ($data as $key) { ?>
    <tr>
        <td><?=$id?></td>
        <td><small><?=$key['column_name1']?></small></td>
        <td><small><?=$key['column_name2']?></small></td>
        <td><small><?=$key['column_name3']?></small></td>
        <td><small><?=$key['column_name4']?></small></td>
        <?php
              echo "<td><a href='" . base_url() . "/stupreregedit/" . $key['id'] . "' class='btn btn-sm btn-outline-primary rounded-pill'><i class='bi bi-pencil-square'></i> &nbsp;Edit</a>&nbsp;&nbsp;
                <a href='" . base_url() . "/stupreregdel/" . $key['id'] . "' class='btn btn-sm btn-outline-danger rounded-pill'><i class='bi bi-trash'></i>&nbsp;Delete</a></td>"; ?>
    </tr>
<?php $id++; } ?>
</tbody>
</table>
 <div id="pagination-links" class="p-3" 
      hx-boost="true" 
      hx-target="#search-results">  
        <?= $pager->links() ?>
    </div>  