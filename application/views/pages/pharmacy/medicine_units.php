<?php $this->load->view('templates/base_header.php');

$csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
); ?>
<section class="section">
  <div class="row">
    
  <div class="col-lg-12">
      <div class="collapse" id="addUnitCollapse">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"> Medicine units/dosage registration form</h5>
            <form class="row g-3" method="post" action="javascript:void(0);" id="units_form">
              <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
              <div class="col-md-12">
              <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" placeholder="eg. mass, volume, etc." class="form-control"/>
              </div>
              <div class="col-md-6">
                <label for="token" class="form-label">Unit</label>
                <input type="text" name="unit" id="unit" placeholder="eg. mg, g, ml, etc." class="form-control" />
              </div>
              <div class="col-md-6">
                <label for="token" class="form-label">Token</label>
                <input type="text" name="token" id="token" placeholder="start next to last token" class="form-control numberonly" />
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-secondary"> Reset </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">List of medicine units/dosage</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addUnitCollapse">
              <i class="bi bi-plus-circle me-1"></i> Add New
            </button>
          </div>
          
          <table id="table_units" class="table table-striped table-sm nowrap"  style="width:100%">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Unit</th>
                <th scope="col">Token</th>
                <th scope="col">Author</th>
                <th scope="col">Date Created</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>

        </div>
      </div>
      
    </div>

  </div>
</section>
<?php $this->load->view('templates/base_footer.php'); ?>

<script type="text/javascript">
  $(function() {

   // Number only input
    $('.numberonly').keypress(function (e) {
      var charCode = (e.which) ? e.which : event.keyCode
      if (String.fromCharCode(charCode).match(/[^0-9]/g))
        return false;
    });

  // Medicine categories on page load with datatable jquery library
  const title = '<?php echo $title; ?>';
  const header = '<?php echo $header; ?>';
  const messageTop = `${header} | List of medicine units`;
  var table_units = $('#table_units').DataTable({
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
      url : "<?php echo base_url('pharmacy/medicine-units');?>",
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


$("#units_form").validate({
  debug: false,
  errorClass: "text-danger",
  errorElement: "span",
  rules: { 
    token: { required: true, number: true },
    title: { required: true },
    unit: { required: true },
  },
  messages: {},
  highlight: function(element, errorClass) {
    $(element).removeClass(errorClass);
  },
  submitHandler: function () {
    var dialog = bootbox
    .dialog({
      message:
      '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
      closeButton: false,
    })
    .on("shown.bs.modal", function () {
      var formdata = $("#units_form").serialize();
      $.ajax({
        url: '<?php echo base_url('pharmacy/save-medicine-units');?>',
        type: "POST",
        data: formdata,
        dataType: "JSON",
        success: function (response) {
          if (response.status) {
            bootbox.alert(response.data.toString(), function () {
              $("#units_form")[0].reset();
              $('#addUnitCollapse').collapse('hide');
              table_units.ajax.reload();
              dialog.modal("hide");
            });
          } else {
            bootbox.alert(response.data.toString(), function () {
              dialog.modal("hide");
            });
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          bootbox.alert(errorThrown.toString(), function () {
            dialog.modal("hide");
          });
        },
        timeout: 10000
      });
    });
  },
});

});
</script>