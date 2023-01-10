<?php $this->load->view('templates/base_header.php'); 
$csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
);
?>
<section class="section">
  <div class="row">

    <div class="col-lg-12">
      <div class="collapse" id="addDiseaseCollapse">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"> Disease imports section</h5>
            <form class="row g-3" id="form-upload-disease" method="post" autocomplete="off">
              <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
              <div class="col-md-9">
                <input class="form-control" type="file" id="file" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
              </div>
              <div class="col-md-3">
                <button type="submit" id="btnUpload" class="btn btn-primary waves-effect waves-light">Submit</button>
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
            <h5 class="card-title">List of diseases</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addDiseaseCollapse">
              <i class="bi bi-plus-circle me-1"></i> Import Diseases
            </button>
          </div>

          <table id="table_diseases" class="table table-striped table-sm nowrap" style="width:100%">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Title</th>
                <th scope="col">Code</th>
                <th scope="col">Communicable</th>
                <th scope="col">Action</th>
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


    const title = 'List of patient diseases'
    var table_diseases = $('#table_diseases').DataTable({
      oLanguage: {
        sProcessing: "loading...",
        sLengthMenu: 'Show <select class="form-select">'+
        '<option value="10">10</option>'+
        '<option value="50">50</option>'+
        '<option value="100">100</option>'+
        '<option value="500">500</option>'+
        '<option value="1000">1000</option>'+
        '<option value="-1">All</option>'+
        '</select> records'
      },
      "responsive": true,
      "processing":true,
      "serverSide":true,
      "order":[],
      "ajax": {
        url : "<?php echo base_url('disease/admin/get-diseases');?>",
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


// Save disease
$("#disease_form").validate({
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
    category: { required: true, number: true },
    alias: { required: true },
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
      var formdata = $("#disease_form").serialize();
      $.ajax({
        url: '<?php echo base_url('disease/add-disease');?>',
        type: "POST",
        data: formdata,
        dataType: "JSON",
        success: function (response) {
          table_diseases.ajax.reload();
          if (response.status) {
            $("#disease_form")[0].reset();
            $('#addDiseaseCollapse').collapse('hide');
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


$("#table_diseases tbody").on('click', 'button', function() {
 var id = $(this).attr('data-id');
 var disease = $(this).attr('data-text');
 var action = this.name;

 if(action == "comButton" || action == "notComButton" || action == "DelButton") {

  const message = action == "comButton" ? `Set communicable a medicine with title <code>${disease}</code>?` : (action == "notComButton" ? `Set non-communicable a medicine with title <code>${disease}</code>?` : `Delete a medicine with title <code>${disease}</code>?`);

  const state = action == "comButton" ? 1 : (action == "notComButton" ? 0 : '');

  const url = '<?php echo base_url("disease/communicable-state/");?>'+state+'/'+id;
  const url2 = '<?php echo base_url('disease/admin/delete-disease/');?>'+id;

  const urls = action == "comButton" ? url : (action == "notComButton" ? url : url2);

  bootbox.confirm({
    message: message,
    buttons: {
      confirm: {
        label: '<i class="fa fa-check"></i> Agree',
        className: "btn-success",
      },
      cancel: {
        label: '<i class="fa fa-times"></i> Disagree',
        className: "btn-danger",
      },
    },
    callback: function (result) {
      if (result == true) {
       var dialog = bootbox
       .dialog({
        message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
        closeButton: false,
      }).on("shown.bs.modal", function () {
        $.ajax({
          url: urls,
          type: "POST",
          data: {},
          dataType: "JSON",
          success: function (response) {
            bootbox.alert(response.data.toString(), function () {
              table_diseases.ajax.reload();
              dialog.modal("hide");
            });
          },
          error: function (jqXHR, textStatus, errorThrown) {
            bootbox.alert(errorThrown.toString(), function () {
              table_diseases.ajax.reload();
              dialog.modal("hide");
            });
          },
        });
      });
    }
  }
});
 }
});


// $("#table_diseases tbody2").on('click', 'button', function() {
//  var id = $(this).attr('data-id');
//  var disease = $(this).attr('data-text');

//  if(this.name == "DelButton") {
//    bootbox.confirm({
//     message: `Delete a medicine with title <code>${disease}</code>?`,
//     buttons: {
//       confirm: {
//         label: '<i class="fa fa-check"></i> Yes',
//         className: "btn-success",
//       },
//       cancel: {
//         label: '<i class="fa fa-times"></i> No',
//         className: "btn-danger",
//       },
//     },
//     callback: function (result) {
//       if (result == true) {
//        var dialog = bootbox
//        .dialog({
//         message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
//         closeButton: false,
//       }).on("shown.bs.modal", function () {
//         $.ajax({
//           url: '<f?php echo base_url('disease/admin/delete-disease/');?>'+id,
//           type: "POST",
//           data: {},
//           dataType: "JSON",
//           success: function (response) {
//             bootbox.alert(response.data.toString(), function () {
//               table_diseases.ajax.reload();
//               dialog.modal("hide");
//             });
//           },
//           error: function (jqXHR, textStatus, errorThrown) {
//             bootbox.alert(errorThrown.toString(), function () {
//               table_diseases.ajax.reload();
//               dialog.modal("hide");
//             });
//           },
//         });
//       });
//     }
//   }
// });
//  }
// });


$("body").on("submit", "#form-upload-disease", function(e) {
  e.preventDefault();
  var data = new FormData(this);
  var dialog = bootbox.dialog({
    message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
    closeButton: false,
  }).on("shown.bs.modal", function () {
    $("#btnUpload").prop('disabled', true);
    $.ajax({
      type: 'POST',
      url: "<?php echo base_url('disease/admin/import-disease') ?>",
      data: data,
      dataType: 'json',
      contentType: false,
      cache: false,
      processData:false, 
      success: function(response) {
        bootbox.alert(response.data.toString(), function () {
          table_diseases.ajax.reload();
          $("#btnUpload").prop('disabled', false);
          dialog.modal("hide");
        });
      }
    });
  });
});



});
</script>