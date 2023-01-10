<?php $this->load->view('templates/base_header.php'); ?>
<section class="section">
  <div class="row">

    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">A list of treated patients</h5>
          </div>

          <table id="table_treated" class="table table-striped table-sm nowrap" style="width:100%">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Patient Name</th>
                <th scope="col">Patient PF</th>
                <th scope="col">Mobile</th>
                <th scope="col">Sex</th>
                <th scope="col">Time-In</th>
                <th scope="col">Time-Out</th>
                <th scope="col">Span</th>
                <th scope="col">Occupation</th>
                <th scope="col">Address</th>
                <th scope="col">Emergency Contact</th>
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
    
    $('.numberonly').keypress(function (e) {
      var charCode = (e.which) ? e.which : event.keyCode
      if (String.fromCharCode(charCode).match(/[^0-9]/g))
        return false;
    });
    
    
    const title = 'A list of treated patients';
    var table_treated = $('#table_treated').DataTable({
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
        url : "<?php echo base_url('reports/served-patients/'.@$header); ?>",
        type : 'POST'
      },
      "ordering": false,
      "dom": 'Blfrtip',
      "buttons": [
      { extend: "copy", title: title,},
      { extend: "excel", title: title, },
      { extend: "csv", title: title, },
      { extend: "pdf", title: title},
      { extend: "print", title: title, },
      ],
    });
    
    // setInterval(function() {
    //   table_treated.ajax.reload();
    // }, 60000);
    
    
  });
</script>