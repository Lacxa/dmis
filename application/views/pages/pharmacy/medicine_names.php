<?php $this->load->view('templates/base_header.php');

$csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
); ?>
<section class="section">
  <div class="row">

    <div class="col-lg-12">
      <div class="collapse" id="addNameCollapse">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"> Medicine names registration form</h5>
            <form class="row g-3" method="post" action="javascript:void(0);" id="names_form">
              <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
              <div class="col-md-12">
                <input type="text" name="name" id="name" class="form-control" placeholder="Medicine name - in generic name" />
              </div>
              <div class="col-md-6">
                <input type="text" name="slag" id="slag" class="form-control" placeholder="Medicine slag" />
              </div>
              <div class="col-md-6">
                <input type="text" name="token" id="token" class="form-control numberonly" placeholder="Medicine token" />
              </div>
              <div class="col-md-12">
                <select name="category" id="category" class="form-select">
                  <option selected> Choose category...</option>
                  <?php foreach ($categories as $row) { ?>
                    <option value="<?php echo $row->token;?>"><?php echo $row->title;?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-md-12">
                <select name="format" id="format" class="form-select">
                  <option selected> Choose format...</option>
                  <?php foreach ($formats as $row) { ?>
                    <option value="<?php echo $row->token;?>"><?php echo $row->title;?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-md-12">
                <textarea name="desc" id="desc" class="form-control"> </textarea>
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
            <h5 class="card-title">List of medicine generic names</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addNameCollapse">
              <i class="bi bi-plus-circle me-1"></i> Add Name
            </button>
          </div>
          
          <table id="table_names" class="table table-striped table-sm nowrap"  style="width:100%">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Token</th>
                <th scope="col">Category</th>
                <th scope="col">Format</th>
                <th scope="col">State</th>
                <th scope="col">Date Created</th>
                <th scope="col">Author</th>
                <th scope="col">Decsription</th>
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
    const messageTop = `${header} | List of Medicine Generic Names`;
    var table_names = $('#table_names').DataTable({
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
        url : "<?php echo base_url('pharmacy/medicine-names');?>",
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
    
    
    $("#names_form").validate({
  errorPlacement: function(error, element) {
    error.addClass('text-danger');
  },
  debug: false,
  errorClass: "is-invalid",
  validClass: "is-valid",
  errorElement: "div",
      rules: { 
        token: { required: true, number: true },
        name: { required: true },
        slag: { required: true },
        category: { required: true, number: true },
        format: { required: true, number: true },
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
          var formdata = $("#names_form").serialize();
          $.ajax({
            url: '<?php echo base_url('pharmacy/save-medicine-names');?>',
            type: "POST",
            data: formdata,
            dataType: "JSON",
            success: function (response) {
              table_names.ajax.reload();
              if (response.status) {
                bootbox.alert(response.data.toString(), function () {
                  $("#names_form")[0].reset();
                  $('#addNameCollapse').collapse('hide');
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
    
    
    $("#table_names tbody").on('click', 'button', function() {
      var id = $(this).attr('data-id');
      var name = $(this).attr('data-name');
      if(this.name == "activateButton") {
        bootbox.confirm({
          message: `Activate a medicine with generic name as ${name}?`,
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
                message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                closeButton: false,
              }).on("shown.bs.modal", function () {
                $.ajax({
                  url: '<?php echo base_url('pharmacy/medicine-state/1/');?>'+id,
                  type: "POST",
                  data: {},
                  dataType: "JSON",
                  success: function (response) {
                    bootbox.alert(response.data.toString(), function () {
                      table_names.ajax.reload();
                      dialog.modal("hide");
                    });
                  },
                  error: function (jqXHR, textStatus, errorThrown) {
                    bootbox.alert(errorThrown.toString(), function () {
                      table_names.ajax.reload();
                      dialog.modal("hide");
                    });
                  },
                });
              });
              
            }
          }
        });
      } else if(this.name == "deactivateButton"){
        bootbox.confirm({
          message: `Disable a medicine with generic name as ${name}?`,
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
                message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                closeButton: false,
              }).on("shown.bs.modal", function () {
                $.ajax({
                  url: '<?php echo base_url('pharmacy/medicine-state/0/');?>'+id,
                  type: "POST",
                  data: {},
                  dataType: "json",
                  success: function (response) {
                    bootbox.alert(response.data.toString(), function () {
                      table_names.ajax.reload();
                      dialog.modal("hide");
                    });
                  },
                  error: function (jqXHR, textStatus, errorThrown) {
                    bootbox.alert(errorThrown.toString(), function () {
                      table_names.ajax.reload();
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
    
    
  });
</script>