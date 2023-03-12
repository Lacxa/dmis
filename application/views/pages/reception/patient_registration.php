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
          <h5 class="card-title">Search Patient</h5>
          <!-- Search Patient Start -->
          <div class="row">
            <div class="col-lg-10 col-md-12 col-sm-12">
              <input type="text" id="search_patient" name="search_patient" placeholder="Enter keyword (eg. patient file number, name, phone, NHIF card number)" class="form-control" required>
            </div>
            <div class="col-lg-2 col-md-12 col-sm-12">
              <button type="button" id="regFormBtn" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Add New</button>
            </div>
          </div>
          <!-- Search Patient End -->

          <!-- Search results area -->
          <ul class="list-group mt-2" id="patient_search_results"></ul>
        </div>
      </div>

      <div class="card" style="display:none;" id="registration-form">
        <div class="card-body">
          <h5 class="card-title">Patient Registration Area</h5>

          <!-- Multi Columns Form -->
          <form class="row g-3" method="post" action="javascript:void(0);" id="reg_form">

            <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />

            <div class="col-md-4">
              <label for="file_no" class="form-label"> Patient File No: <span class="text-primary">(Auto Generated)</span> <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="file_no" id="file_no"/>
            </div>

            <div class="col-md-4">
              <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="first_name" name="first_name"/>
            </div>

            <div class="col-md-4">
              <label for="middle_name" class="form-label">Middle Name</label>
              <input type="text" class="form-control" id="middle_name" name="middle_name"/>
            </div>
            
            <div class="col-md-4">
              <label for="last_name" class="form-label">Last name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="last_name" name="last_name"/>
            </div>

            <div class="col-md-4">
              <div class="container">
                <label for="dob" class="form-label">Birth date <span class="text-danger">*</span></label>
                <div class="datepicker input-group date">
                  <input type="text" class="form-control" placeholder="Choose a date" name="dob" id="dob" autocomplete="off"/>
                  <span class="input-group-append" id="group">
                    <span class="input-group-text bg-light d-block">
                      <i class="bi bi-calendar3"></i>
                    </span>
                  </span>
                </div>
              </div>
            </div>
            
            <div class="col-md-4">
              <label for="inputPassword5" class="form-label">Sex <span class="text-danger">*</span></label>
              <select id="gender" name="gender" class="form-select">
                <option value="" selected>Choose...</option>
                <option value="MALE">MALE</option>
                <option value="FEMALE">FEMALE</option>
              </select>
            </div>

            <div class="col-md-4">
              <label for="inputEmail5" class="form-label">Occupation <span class="text-danger">*</span></label>
              <select name="occupation" id="occupation" class="form-select">
                <option value="" selected>Choose...</option>
                <option value="EMPLOYEE">EMPLOYEE</option>
                <option value="STUDENT">STUDENT</option>
                <option value="OTHER">OTHER</option>
              </select>
            </div>

            <div class="col-md-4">
              <label for="inputPassword5" class="form-label">Phone <span class="text-danger">*</span></label>
              <input type="text" class="form-control numberonly" id="phone" name="phone" />
            </div>

            <div class="col-md-4">
              <label for="inputEmail5" class="form-label">Address <span class="text-danger">*</span></label> 
              <input type="text" class="form-control" id="address" name="address" />
            </div>

            <div class="col-md-6">
              <label for="em_name" class="form-label">Emergency Contact Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="em_name" name="em_name"/>
            </div>

            <div class="col-md-6">
              <label for="em_phone" class="form-label numberonly">Emergency Contact Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="em_phone" name="em_phone"/>
            </div>

            <div class="col-md-4">
              <label for="inputPassword5" class="form-label">NHIF Card No:</label>
              <input type="text" class="form-control" id="nhif_card" name="nhif_card" />
            </div>

            <div class="col-md-4">
              <label for="inputEmail5" class="form-label">NHIF Authorization No:</label> 
              <input type="text" class="form-control" id="nhif_auth" name="nhif_auth" />
            </div>

            <div class="col-md-4">
              <label for="inputPassword5" class="form-label">Vote No:</label>
              <input type="text" class="form-control" id="vote_no"  name="vote_no" />
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
              <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
          </form>
          <!-- End Multi Columns Form -->
        </div>
      </div>


    </div>
  </div>
</section>

<?php $this->load->view('templates/base_footer.php'); ?>

<script>
  $(function () {
    $('.datepicker').datepicker({
      todayHighlight: true,
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


    // Display registration form
    $("#regFormBtn").click(function () {
      $('#patient_search_results').html("");
      $("div#registration-form").toggle();
      $('div#registration-form input#file_no').prop('readonly', true);
      $("#reg_form")[0].reset();
    });

    // Search patient
    $('#search_patient').keyup(function() {
      var search = $(this).val();
      if(search){
        $.ajax({
          url:"<?php echo base_url('reception/search-patient');?>",
          method:"POST",
          data:{ search_keyword: search },
          dataType: 'json',
          delay: 250,
          success:function(data)
          {
            $('#patient_search_results').html("");
            if(data.length > 0){
              $.each(data, function(key, value) {
                $("#patient_search_results").append(`
                  <a href="#" name="searchedPatientLink" data-id="${value.id}" 
                  data-name="${value.first_name} ${value.last_name}"
                  class="list-group-item list-group-item-action">${value.first_name} ${value.middle_name == null ? '' : value.middle_name} 
                  ${value.last_name} | ${value.pf} | ${value.phone} | ${value.address} ${value.nhif == null ? '' : '| '+value.nhif}</a>
                  `);
              });
            }
            else{
              $('#patient_search_results').append('<li class="list-group-item text-danger">Oops!, no results. Please press "Add New" button to add a fresh client </li>');
            }
          }
        });
      }else{
        $('#patient_search_results').html("");
      }
    });


    // Get the data of the selected Patient
    $("ul#patient_search_results").on('click', 'a', function() {
      var client_id = $(this).attr('data-id');
      var client_name = $(this).attr('data-name');
      if(this.name == "searchedPatientLink") {
        bootbox.confirm({
          message:'You selected <strong>'+client_name+'</strong>',
          buttons: 
          {
            confirm: {label: '<i class="fa fa-check"></i> Agree',className: "btn-success",},
            cancel: {label: '<i class="fa fa-times"></i> Disagree',className: "btn-danger",},
          },
          callback: function (result) 
          {
            if (result == true)
            {
              var dialog = bootbox.dialog({
               message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
               closeButton: false,
             }).on("shown.bs.modal", function () {
              $.ajax({
                url: '<?php echo base_url("reception/get-patient-by-id"); ?>',
                type: "POST",
                dataType: "json",
                data: { patient_id: client_id },
                success: function (response) {
                  $("#reg_form")[0].reset();
                  if(response.status){
                    $('input#search_patient').val("");
                    $('#patient_search_results').html("");
                    dialog.modal("hide");
                    const patient = response.data;

                    // var dob = new Date(patient.pat_dob);
                    // var day = ("0" + dob.getDate()).slice(-2);
                    // var month = ("0" + (dob.getMonth() + 1)).slice(-2);
                    // var final_dob = dob.getFullYear()+"-"+(month)+"-"+(day);

                    $('div#registration-form').show();
                    console.log(patient)

                    $('div#registration-form input#file_no').val(patient.pat_file_no);
                    $('div#registration-form input#file_no').prop('readonly', true);
                    $('div#registration-form input#first_name').val(patient.pat_fname);
                    $('div#registration-form input#middle_name').val(patient.pat_mname);
                    $('div#registration-form input#last_name').val(patient.pat_lname);
                    $('div#registration-form input#dob').val(patient.pat_dob);
                    $("div#registration-form select#gender").val(patient.pat_gender).change();
                    $("div#registration-form select#occupation").val(patient.pat_occupation).change();
                    $('div#registration-form input#phone').val(patient.pat_phone);
                    $('div#registration-form input#address').val(patient.pat_address);
                    $('div#registration-form input#em_name').val(patient.pat_em_name);
                    $('div#registration-form input#em_phone').val(patient.pat_em_number);
                    $('div#registration-form input#nhif_card').val(patient.pat_nhif_card_no);
                    $('div#registration-form input#nhif_auth').val(patient.pat_nhif_auth_no);
                    $('div#registration-form input#vote_no').val(patient.pat_vote_no);

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


    // Patient registration submit
    $("#reg_form").validate({
      errorPlacement: function(error, element) {
        error.addClass('text-danger');
        // error.insertAfter(element.parent('div'));
        // error.insertAfter(element.next('span'));
        
        if (element.attr("name") == "dob") {
          // error.insertAfter("#lastname");
          error.insertAfter(element.parent('div'));
        } else {
          error.insertAfter(element);
        }
      },
      debug: false,
      errorClass: "is-invalid",
      validClass: "is-valid",
      errorElement: "div",
      rules: { 
        first_name: { required: true, maxlength: 20 },
        middle_name: { maxlength: 20 },
        last_name: { required: true, maxlength: 20 },
        dob: { required: true },
        gender: { required: true },
        occupation: { required: true },
        phone: { required: true, maxlength: 10, minlength: 10 },
        address: { required: true, maxlength: 40 },
        em_name: { required: true, maxlength: 40 },
        em_phone: { required: true, maxlength: 10, minlength: 10 },
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
          var formdata = $("#reg_form").serialize();
          $.ajax({
            url: '<?php echo base_url('reception/patient-registration');?>',
            type: "POST",
            data: formdata,
            dataType: "JSON",
            success: function (response) {
              if (response.status) {
                $("#reg_form")[0].reset();
                $('div#registration-form').hide();
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