<?php

$this->load->view('templates/base_header.php');

$csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
);

?>
<section class="section">
  <div class="row">

    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">List of patient visits</h5>

          <!-- Table with stripped rows -->
          <table id="table_patients" class="table table-striped table-sm nowrap"  style="width:100%">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">PF</th>
                <th scope="col">Mobile</th>
                <th scope="col">Entry Time</th>
                <th scope="col">Status</th>                
                <th scope="col">BP</th>
                <th scope="col">PR</th>
                <th scope="col">Respiration Rate</th>
                <th scope="col">Weight</th>
                <th scope="col">Height</th>
                <th scope="col">Temperature</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>

        </div>
      </div>
      
    </div>

    <div class="modal fade" id="patientInitialsModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="post" id="preliminaries" action="">

            <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />

            <div class="modal-header">
              <h5 class="modal-title">Vital Signs</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row g-3">

                <div class="col-md-6" style="display: none;">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">ID</span>
                    <input type="text" id="record_id" name="record_id" class="form-control" aria-label="Record ID" aria-describedby="basic-addon1">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">@</span>
                    <input type="text" id="patient_name" name="patient_name" class="form-control" aria-label="Username" aria-describedby="basic-addon1" disabled>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">#</span>
                    <input type="text" id="patient_file" name="patient_file" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">BP</span>
                    <input type="text" id="blood_pressure" name="blood_pressure" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                    <span class="input-group-text">mmHg</span>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Pulse Rate</span>
                    <input type="text" id="pulse_rate" name="pulse_rate"  class="form-control allow_decimal" aria-label="Username" aria-describedby="basic-addon1">
                    <span class="input-group-text">bpm</span>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Weight</span>
                    <input type="text" id="weight" name="weight" class="form-control allow_decimal" aria-label="Username" aria-describedby="basic-addon1">
                    <span class="input-group-text">kg</span>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Height</span>
                    <input type="text" id="height" name="height" class="form-control allow_decimal" aria-label="Username" aria-describedby="basic-addon1">
                    <span class="input-group-text">cm</span>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Temperature</span>
                    <input type="text" id="temperature" name="temperature" class="form-control allow_decimal" aria-label="Username" aria-describedby="basic-addon1">
                    <span class="input-group-text">&deg;C</span>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Respiration Rate</span>
                    <input type="text" id="respiration" name="respiration" class="form-control allow_decimal" aria-label="respiration" aria-describedby="basic-addon1">
                    <span class="input-group-text">bpm</span>
                  </div>
                </div>

                <div class="input-group col-md-12">
                  <label class="input-group-text" for="inputGroupSelect01">CARE</label>
                    <select id="care" name="care" class="form-select" id="inputGroupSelect01">
                      <option value="0" selected>OUTPATIENT</option>
                      <option value="1">INPATIENT</option>
                    </select>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</section>
<?php $this->load->view('templates/base_footer.php'); ?>

<script type="text/javascript">
  $(function() {

    // Numbers and decimals only input
    $(".allow_decimal").on("input", function(evt) {
     var self = $(this);
     self.val(self.val().replace(/[^0-9\.]/g, ''));
     if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
     {
       evt.preventDefault();
     }
   });


  //   $(".blood_pressure").on("input", function(evt) {
  //    var self = $(this);
  //   //  self.val(self.val().replace(/\d{1,3}\/\d{1,3}$/, ''));
  //  });

  // Retrieve patient data on page load
  const title = 'Reception section | List of Patient Visits'
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
      url : "<?php echo base_url('reception/my-patients');?>",
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
      exportOptions: {
        columns: [0, 1, 2, 3, 4, 5],
      },
    },
    {
      extend: "print",
      title: title,
    },
    ],
  });


  // Open modal when incomplete button/row is clicked
  $("#table_patients tbody").on('click', 'button', function() {
   var id = $(this).attr('data-id');
   var name = $(this).attr('data-patient');
   var file_no = $(this).attr('data-file');
   if(this.name == "statusButton") {
    $('#patientInitialsModal').modal('show').on('shown.bs.modal', function () {                    
      $("form#preliminaries input#record_id").val(id);
      $("form#preliminaries input#patient_name").val(name);
      $("form#preliminaries input#patient_file").val(file_no);
      $('form#preliminaries input#patient_file').prop('readonly', true);
    });
  } else if(this.name == "deleteBtn"){

    bootbox.confirm({
      message: 'Are you sure?',
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
            url: '<?php echo base_url('reception/delete-patient/');?>'+id,
            type: "POST",
            data: {},
            dataType: "JSON",
            success: function (response) {
              bootbox.alert(response.data.toString(), function () {
                table_patients.ajax.reload();
                dialog.modal("hide");
              });
            },
            error: function (jqXHR, textStatus, errorThrown) {
              bootbox.alert(errorThrown.toString(), function () {
                table_patients.ajax.reload();
                dialog.modal("hide");
              });
            },
          });
        });

      }
    }
  });

  } else {}
});


// Submit preliminaries
$("#preliminaries").validate({
  errorPlacement: function(error, element) {
    error.addClass('text-danger');
    error.insertAfter(element.parent('div'));
    // error.insertAfter(element.next('span'));
  },
  debug: false,
  errorClass: "is-invalid",
  validClass: "is-valid",
  errorElement: "div",
  rules: { 
    record_id: { required: true },
    patient_file: { required: true },
    blood_pressure: { required: true },
    pulse_rate: { required: true, range: [40, 171] },
    weight: { required: true, range: [1.5, 727] },
    height: { required: true, range: [22, 500] },
    temperature: { required: true, range: [28, 41] },
    respiration: { required: true, range: [9, 61] },
    care: { required: true, number: true, range: [0, 1] },
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
      var formdata = $("#preliminaries").serialize();
      $.ajax({
        url: '<?php echo base_url('reception/patient-preliminaries');?>',
        type: "POST",
        data: formdata,
        dataType: "JSON",
        success: function (response) {
          table_patients.ajax.reload();
          if (response.status) {
            $("#preliminaries")[0].reset();
            $('#patientInitialsModal').modal('hide');
            bootbox.alert(response.data.toString(), function () {
              dialog.modal("hide");
            });
          } else {
            bootbox.alert(response.data.toString(), function () {
              table_patients.ajax.reload();
              dialog.modal("hide");
            });
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          bootbox.alert(errorThrown.toString(), function () {
            table_patients.ajax.reload();
            dialog.modal("hide");
          });
        },
      });      
    });
  },
});

});
</script>