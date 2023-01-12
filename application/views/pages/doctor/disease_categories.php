<?php $this->load->view('templates/base_header.php'); ?>
<section class="section">
  <div class="row">
    
 <!--  <div class="col-lg-12">
      <div class="collapse" id="addCategoryCollapse">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"> Disease category registration form</h5>
            <form class="row g-3" method="post" action="javascript:void(0);" id="category_form">
              <div class="col-6">
              <label for="title" class="form-label">Category Name</label>
                <input type="text" name="title" id="title" class="form-control"/>
              </div>
              <div class="col-6">
              <label for="token" class="form-label">Token</label>
                <input type="text" name="token" id="token" class="form-control numberonly" />
              </div>
              <div class="col-md-12">
              <label for="desc" class="form-label">Description</label>
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
    </div> -->

    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">List of disease categories</h5>
            <!-- <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addCategoryCollapse">
              <i class="bi bi-plus-circle me-1"></i> Add Category
            </button> -->
          </div>
          
          <table id="table_categories" class="table table-striped table-sm"  style="width:100%">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col">Token</th>
                <th scope="col">Post Date</th>
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

  // Disease categories on page load with datatable jquery library
  const title = '<?php echo $title; ?>';
  const header = '<?php echo $header; ?>';
  const messageTop = `${header} | List of Disease categories`;
  var table_categories = $('#table_categories').DataTable({
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
      url : "<?php echo base_url('disease/disease-categories');?>",
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


$("#category_form").validate({
  errorPlacement: function(error, element) {
    error.insertAfter(element.parent('div'));
  },
  debug: false,
  errorClass: "is-invalid",
  validClass: "is-valid",
  errorElement: "div",
  rules: { 
    token: { required: true, number: true },
    title: { required: true },
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
      var formdata = $("#category_form").serialize();
      $.ajax({
        url: '<?php echo base_url('disease/add-disease-category');?>',
        type: "POST",
        data: formdata,
        dataType: "JSON",
        success: function (response) {
          table_categories.ajax.reload();
          if (response.status) {
            bootbox.alert(response.data.toString(), function () {
            $("#category_form")[0].reset();
            $('#addCategoryCollapse').collapse('hide');
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