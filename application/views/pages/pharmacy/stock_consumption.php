<?php $this->load->view('templates/base_header.php');

$csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
); ?>
<section class="section">
  <div class="row">
    
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">A list of <?php echo @$details->medicine1;?> consumers<br /><code><?php echo 'Entry of '.date('Y-m-d', strtotime($details->entry2));?></code></h5>
          </div>
          
          <div class="table-responsive">
            <table id="table_consumption" class="table table-striped table-sm">
              <thead>
                <tr>
                  <th scope="col">Patient</th>
                  <th scope="col">File</th>
                  <th scope="col">Gender</th>
                  <th scope="col">Mobile</th>
                  <th scope="col">Occupation</th>
                  <th scope="col">Date Sold</th>
                  <th scope="col">Count</th>
                </tr>
              </thead>
              <tbody></tbody>
              <tfoot align="right">
                <tr>
                  <th colspan="6"></th><th></th>
                </tr>
              </tfoot>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<?php $this->load->view('templates/base_footer.php'); ?>

<script type="text/javascript">
  $(function() {


    const title = '<?php echo $title; ?>';
    const messageTop = 'A list of <?php echo @$details->medicine1;?> consumers<br /><?php echo 'Entry of '.date('Y-m-d', strtotime($details->entry));?>';
    var table_consumption = $('#table_consumption').DataTable({
      "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // converting to interger to find total
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
        
       var friTotal = api
                .column( 6 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
      
        
            // Update footer by showing the total with the reference of the column index 
      $( api.column( 0 ).footer() ).html('Total');
            $( api.column( 6 ).footer() ).html(friTotal);
        },
      oLanguage: {
        sProcessing: "loading...",
        sLengthMenu: 'Show <select class="form-select">'+
      '<option value="10">10</option>'+
      '<option value="50">50</option>'+
      '<option value="100">100</option>'+
      '<option value="500">500</option>'+
      '<option value="-1">All</option>'+
      '</select> records'
      },
      responsive: true,
      "processing":true,
      "serverSide":true,
      "order":[],
      "ajax": {
        url : "<?php echo base_url('pharmacy/stock-get-sold/'.$details->token);?>",
        type : 'POST'
      },
      "ordering": false,
      "dom": 'Blfrtip',
      "buttons": [
      {
        extend: "copy",
        title: title,
        messageTop: messageTop,
      },
      {
        extend: "excel",
        title: title,
        messageTop: messageTop,
      },
      {
        extend: "csv",
        title: title,
        messageTop: messageTop,
      },
      {
        extend: "pdf",
        title: title,
        messageTop: messageTop,
      },
      {
        extend: "print",
        title: title,
        messageTop: messageTop,
      },
      ],
    });    
    
  });
</script>