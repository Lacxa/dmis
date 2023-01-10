<?php $this->load->view('templates/base_header.php');
$csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
);
 ?>
<section class="section">
  <div class="row">

    <div class="col-lg-12">
      <div class="collapse" id="addComplaintCollapse">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"> Chief complaints configuration section</h5>
            <form class="row g-3" method="post" action="javascript:void(0);" id="complaint_form">
              <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
              <div class="col-md-6">
                <input type="text" name="name" class="form-control" placeholder="Complaint Name"/>
              </div>
              <div class="col-md-6">
                <input type="text" name="token" class="form-control numberonly" placeholder="Complaint Token"/>
              </div>
              <div class="col-md-12">
                <textarea name="desc" class="form-control"></textarea> 
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
            <h5 class="card-title">List of chief complaints</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addComplaintCollapse">
              <i class="bi bi-plus-circle me-1"></i> Add Complaint
            </button>
          </div>

            <table id="table_complaints" class="table table-striped table-sm nowrap" style="width:100%">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Name</th>
                  <th scope="col">Token</th>
                  <th scope="col">Author</th>
                  <th scope="col">Description</th>
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

  // Retrieve complaints data on page load
  const title = 'List of chief complaints'
  var table_complaints = $('#table_complaints').DataTable({
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
      url : "<?php echo base_url('complaints/get-complaints');?>",
      type : 'POST'
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


// Save complaint
$("#complaint_form").validate({
  errorPlacement: function(error, element) {
    error.addClass('text-danger');
  },
  debug: false,
  errorClass: "is-invalid",
  validClass: "is-valid",
  errorElement: "div",
  rules: { 
    name: { required: true },
    token: { required: true },
  },
    highlight: function( element, errorClass, validClass ) {
      $(element).addClass(errorClass).removeClass(validClass);
    },
    unhighlight: function( element, errorClass, validClass ) {
      $(element).removeClass(errorClass).addClass(validClass);
    },
    submitHandler: function () {
    var dialog = bootbox
    .dialog({
      message:
      '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
      closeButton: false,
    })
    .on("shown.bs.modal", function () {
      var formdata = $("#complaint_form").serialize();
      $.ajax({
        url: '<?php echo base_url('complaints/add-complaint');?>',
        type: "POST",
        data: formdata,
        dataType: "JSON",
        success: function (response) {
          table_complaints.ajax.reload();
          if (response.status) {
            $("#complaint_form")[0].reset();
            $('#addComplaintCollapse').collapse('hide');
            bootbox.alert(response.data.toString(), function () {
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
      });
    });
  },
});

});
</script>