<?php 
$this->load->view('templates/base_header.php'); 
$error = $this->session->flashdata('error');
$success = $this->session->flashdata('success');
$color = isset($success) ? 'primary' : 'danger';
$message = isset($success) ? $success : $error;

$csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
);
?>
<section class="section">
  <div class="row">
    
    <?php if (validation_errors() != '') { ?>
      <div class="col-12">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo validation_errors();?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>
    <?php } ?>

    <?php if(isset($success) || isset($error)) { ?>
      <div class="col-12">
        <div class="alert alert-<?php echo $color;?> alert-dismissible fade show" role="alert">
          <?php echo $message;?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>
    <?php } ?>
    
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">List of patients</h5>

          <table id="table_patients" class="table table-striped table-sm nowrap" style="width:100%">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Patient</th>
                <th scope="col">Age</th>
                <th scope="col">Address</th>
                <th scope="col">Entry Time</th>
                <th scope="col">Action</th>
                <th scope="col">BP</th>
                <th scope="col">Pulse Rate</th>
                <th scope="col">Weight</th>
                <th scope="col">Height</th>
                <th scope="col">Temp</th>
                <th scope="col">Respiration</th>
                <th scope="col">BMI</th>
                <th scope="col">Refferer</th>
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

  // Retrieve patient data on page load
  const title = 'Lab section | List of Patient Visits'
  var table_patients = $('#table_patients').DataTable({
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
      url : "<?php echo base_url('lab/my-patients');?>",
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


  // Proccess when "serve" button is clicked
  $("#table_patients tbody").on('click', 'button', function() {
   if(this.name == "serveBtn") {
     var id = $(this).attr('data-id');
     var name = $(this).attr('data-name');
     var file_no = $(this).attr('data-pf');
     bootbox.confirm({
      message:
      'Serve <code>' + name + '</code> with file number <code>"' + file_no + '"</code>?',
      buttons: {
        confirm: {
          label: '<i class="fa fa-check"></i> Yes',
          className: "btn-success",
        },
        cancel: {
          label: '<i class="fa fa-times"></i> No',
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {
          var dialog = bootbox
          .dialog({
            message:
            '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Loading...</div>',
            closeButton: false,
          })
          .on("shown.bs.modal", function () {
            $.ajax({
              url: "<?php echo base_url('lab/serve-patient/'); ?>"+id,
              type: "POST",
              dataType: "json",
              data: {},
              success: function (response) {
                table_patients.ajax.reload();
                if (response.status) {
                  bootbox.alert(response.data.toString(), function () {
                    dialog.modal("hide");
                    window.location.href = response.redirect;
                  });
                } else {
                  bootbox.alert(response.data.toString(), function () {
                    dialog.modal("hide");
                  });
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
                table_patients.ajax.reload();
                bootbox.alert(errorThrown.toString(), function () {
                  dialog.modal("hide");
                });
              },
            });
          });
        }
      },
    });
   }
 });

  setInterval(function() {
    table_patients.ajax.reload();
  }, 20000);

});
</script>