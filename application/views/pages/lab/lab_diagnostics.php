<?php $this->load->view('templates/base_header.php'); ?>
<section class="section">
  <div class="row">

    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Laboratory investigation list</h5>
          </div>

            <table id="table_investigations" class="table table-striped table-sm nowrap" style="width:100%">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Name</th>
                  <th scope="col">Code</th>
                  <th scope="col">Unit</th>
                  <th scope="col">Category</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
        </div>
      </div>      
    </div>
  </div>
</section>
<?php $this->load->view('templates/base_footer.php'); ?>

<script type="text/javascript">
  $(function() {

  const title = 'Laboratory investigation list';
  var table_investigations = $('#table_investigations').DataTable({
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
      url: '<?php echo base_url('lab/lab-diagnostics');?>',
      type: 'POST'
    },
    "ordering": false,
    "dom": 'Blfrtip',
    "buttons": [
    {
      extend: "copy",
      title: title,
    },
    {
      extend: "excel",
      title: title,
    },
    {
      extend: "csv",
      title: title,
    },
    {
      extend: "pdf",
      title: title,
    },
    {
      extend: "print",
      title: title,
    },
    ],
  });

});
</script>