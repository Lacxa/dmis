<?php $this->load->view('templates/base_header.php'); 

$csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
);?>

<section class="section">
  <div class="row">
    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Search Record</h5>
          <div class="row">
            <div class="col-12">
              <input type="text" id="search_patient" name="search_patient" placeholder="Enter keyword (eg. patient file number, name, phone, NHIF card number)" class="form-control" required>
            </div>
          </div>

          <!-- Search results area -->
          <ul class="list-group mt-2" id="patient_search_results"></ul>
        </div>
      </div>

      <div class="card" style="display:none;" id="preliminaries">
        <div class="card-body">
          <h5 class="card-title">Vital Signs Modification Form</h5>

          <form class="row g-3" method="post" action="javascript:void(0);" id="preliminary_form">

            <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
            
            <div class="col-md-6" style="display: none;">
              <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">ID</span>
                <input type="number" id="record_id" name="record_id" class="form-control" aria-label="Record ID" aria-describedby="basic-addon1">
              </div>
            </div>

            <div class="col-md-6" style="display: none;">
              <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">MODIFY</span>
                <input type="number" id="modify" name="modify" class="form-control" aria-label="modify" aria-describedby="basic-addon1">
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

            <div class="text-center">
              <button type="submit" class="btn btn-primary">Save Changes</button>
              <span id="delBtn"></span>
            </div>
          </form>
        </div>
      </div>


    </div>
  </div>
</section>

<?php $this->load->view('templates/base_footer.php'); ?>

<script>
  $(function () {
    $('.datepicker').datepicker({
      clearBtn: true,
      autoclose: true,
      changeMonth: true,
      changeYear: true,
      format: "yyyy-mm-dd",
    });
    
  // Number only input
  $('.numberonly').keypress(function (e) {
    var charCode = (e.which) ? e.which : event.keyCode
    if (String.fromCharCode(charCode).match(/[^0-9]/g))
      return false;
  });    
  
  // Numbers and decimals only input
  $(".allow_decimal").on("input", function(evt) {
    var self = $(this);
    self.val(self.val().replace(/[^0-9\.]/g, ''));
    if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
    {
      evt.preventDefault();
    }
  });
  
  // Search patient to modify
  $('#search_patient').keyup(function() {
    var search = $(this).val();
    $('div#preliminaries').hide();
    $('#patient_search_results').html("");
    if(search){
      $.ajax({
        url:"<?php echo base_url('reception/modifications');?>",
        method:"POST",
        data:{ search_keyword: search },
        dataType: 'json',
        delay: 250,
        success:function(data)
        {
          if(data.length > 0){
            $.each(data, function(key, value) {
              $("#patient_search_results").append(`
                <a href="#" name="searchedPatientLink" data-record="${value.record}" data-name="${value.first_name} ${value.last_name}" data-pf="${value.pf}" data-bp="${value.bp}" data-pr="${value.pr}" data-weight="${value.weight}" data-height="${value.height}" data-temp="${value.temp}" data-resp="${value.respiration}" data-status="${value.status}" data-entry="${value.entry}" class="list-group-item list-group-item-action">${value.first_name} ${value.middle_name == null ? '' : value.middle_name} ${value.last_name} | ${value.pf} | ${value.entry} | ${value.address} ${value.nhif == null ? '' : '| '+value.nhif}</a>
                `);
            });
          }
          else{
            $('#patient_search_results').append('<li class="list-group-item text-danger">Oops!, no results.</li>');
          }
        }
      });
    }
  });
  
  
  // Get the data of the selected Patient
  $("ul#patient_search_results").on('click', 'a', function() {
    var client_record = $(this).attr('data-record');
    var client_name = $(this).attr('data-name');
    var client_pf = $(this).attr('data-pf');
    var client_bp = $(this).attr('data-bp');
    var client_pr = $(this).attr('data-pr');
    var client_weight = $(this).attr('data-weight');
    var client_height = $(this).attr('data-height');
    var client_temp = $(this).attr('data-temp');
    var client_resp = $(this).attr('data-resp');
    var client_status = $(this).attr('data-status');
    var client_entry_date = $(this).attr('data-entry');
    if(this.name == "searchedPatientLink") {
      bootbox.confirm({
        message:'You selected <strong>'+client_name+'</strong> with PF:<strong>'+client_pf+'</strong> and visit date:<strong>'+client_entry_date+'</strong>',
        buttons: 
        {
          confirm: {label: '<i class="fa fa-check"></i> Agree',className: "btn-success",},
          cancel: {label: '<i class="fa fa-times"></i> Disagree',className: "btn-danger",},
        },
        callback: function (result) 
        {
          if (result == true)
          {
            $('#patient_search_results').html("");
            $('#search_patient').val("");
            
            $('div#preliminaries').show();            
            $('form#preliminary_form input#record_id').val(client_record);
            $('form#preliminary_form input#modify').val(100);
            $('form#preliminary_form input#patient_name').val(client_name);
            $('form#preliminary_form input#patient_file').val(client_pf);
            $('form#preliminary_form input#patient_file').prop('readonly', true);
            $('form#preliminary_form input#blood_pressure').val(client_bp);
            $('form#preliminary_form input#pulse_rate').val(client_pr);
            $('form#preliminary_form input#weight').val(client_weight);
            $('form#preliminary_form input#height').val(client_height);
            $('form#preliminary_form input#temperature').val(client_temp);
            $('form#preliminary_form input#respiration').val(client_resp);
            
            if(client_status == 'nasubiri_daktari') {
              $("span#delBtn").html(`
                <button type="button" class="btn btn-danger" name="deleteBtn" data-id="${client_record}"> Trash </button>
                `);
            }
          }
        }
      });
    }
  });
  
  // Submit modifications
  $("#preliminary_form").validate({
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
      record_id: { required: true, number: true },
      modify: { required: true, number: true },
      patient_file: { required: true },
      blood_pressure: { required: true },
      pulse_rate: { required: true, range: [40, 171] },
      weight: { required: true, range: [1.5, 727] },
      height: { required: true, range: [22, 500] },
      temperature: { required: true, range: [28, 41] },
    respiration: { required: true, range: [9, 61] },
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
        var formdata = $("#preliminary_form").serialize();
        $.ajax({
          url: '<?php echo base_url('reception/patient-preliminaries');?>',
          type: "POST",
          data: formdata,
          dataType: "JSON",
          success: function (response) {
            if (response.status) {
              $("#preliminary_form")[0].reset();
              $('div#preliminaries').hide();
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
  
  // Delete record
  $("#deleteBtn").click(function() {
    alert("button");
  });
  
  $("div#preliminaries form").on('click', 'button', function() {
    if(this.name == "deleteBtn") {
      var id = $(this).attr('data-id');      
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
                url: '<?php echo base_url('reception/delete-previous-patient/');?>'+id,
                type: "POST",
                data: {},
                dataType: "JSON",
                success: function (response) {
                  if(response.status) {
                    bootbox.alert(response.data.toString(), function () {
                      $("#preliminary_form")[0].reset();
                      $('div#preliminaries').hide();
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
            
          }
        }
      });
    }
  });
  
  
});
</script>