<?php $this->load->view('templates/base_header.php');

$csrf = array(
  'name' => $this->security->get_csrf_token_name(),
  'hash' => $this->security->get_csrf_hash()
); ?>

<section class="section">
  <div class="row">


    <div class="col-lg-12">
      <div class="collapse" id="addDiseaseCollapse">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"> Disease configuration section</h5>
            <form class="row g-3" method="post" action="javascript:void(0);" id="disease_form">
              <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
              <div class="col-md-6">
                <input type="text" name="name" class="form-control" placeholder="Disease Title"/>
              </div>
              <div class="col-md-6">
                <input type="text" name="token" class="form-control" placeholder="ICD-10 Code"/>
              </div>
              <div class="col-md-12">
                <select name="category" id="category" class="form-select">
                  <option selected> Choose category...</option>
                  <?php foreach ($categories as $row) { ?>
                    <option value="<?php echo $row->token;?>"><?php echo $row->text;?></option>
                  <?php } ?>
                </select>
              </div>
              <!-- <div class="col-lg-6 col-md-6">
                <input type="text" name="alias" class="form-control" placeholder="Disease Alias"/>
              </div> -->
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
            <h5 class="card-title">List of diseases</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addDiseaseCollapse">
              <i class="bi bi-plus-circle me-1"></i> Add Disease
            </button>
          </div>

            <table id="table_diseases" class="table table-striped table-sm nowrap" style="width:100%">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Title</th>
                  <th scope="col">Code</th>
                  <th scope="col">Communicable</th>
                  <th scope="col">Author</th>
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

  // Retrieve diseases data on page load
  const title = 'List of common patient diseases'
  var table_diseases = $('#table_diseases').DataTable({
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
      url : "<?php echo base_url('disease/get-diseases');?>",
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
    // alias: { required: true },
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

 if(action == "comButton" || action == "notComButton") {
  const message = action == "comButton" ? 'communicable' : 'non-communicable';
  const state = action == "comButton" ? 1 : 0;

  bootbox.confirm({
    message: `Set ${message} a medicine with title <code>${disease}</code>?`,
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
          url: '<?php echo base_url('disease/communicable-state/');?>'+state+'/'+id,
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

});
</script>