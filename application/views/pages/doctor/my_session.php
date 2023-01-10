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

<section class="section profile">
      <div class="row"> <?php if (validation_errors() != '') {
            echo '<div class="col-12"><div class="alert alert-danger alert-dismissible fade show" role="alert">'. validation_errors() . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>'; } if(isset($success) || isset($error)) { echo '<div class="col-12"><div class="alert alert-' . $color .' alert-dismissible fade show" role="alert">'.$message.'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>';}?>
            
            <div class="col-lg-12" id="session_ids">
                  <input type="number" name="session_record_id" style="display:none;" id="session_record_id" required>
                  <input type="number" name="session_symptom_id" style="display:none;" id="session_symptom_id" required>
                  <input type="number" name="session_visit_id" style="display:none;" id="session_visit_id" required>
            </div>
            <div class="col-lg-12">
                  <div class="card">
                              <div class="card-body pt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                          <h4 class="card-title">Patient Service Area</h4>
                                           <div>
                                                <a type="button" href="<?php echo base_url('doctor/set-in-patient/');?>" class="btn btn-danger" id="set-in-patient">
                                                <i class="bi bi-exclamation-circle me-1"></i>Set In-Patient
                                                </a>
                                                <a type="button" href="<?php echo base_url('doctor/release-patient/'); ?>" class="btn btn-primary" id="release-patient"><i class="bi bi-person-check me-1"></i> Release
                                                </a>
                                          </div>
                                    </div>
                                    <ul class="nav nav-tabs nav-tabs-bordered">
                                          <li class="nav-item">
                                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Vital Signs </button>
                                          </li>
                                          <li class="nav-item">
                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#chief-complaints">Patient History</button>
                                          </li>
                                          <li class="nav-item">
                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#lab-investigations">Lab Investigations</button>
                                          </li>
                                          <li class="nav-item">
                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#disease-section">Diseases</button>
                                          </li>
                                          <li class="nav-item">
                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#medications-section">Medications</button>
                                          </li>
                                    </ul>
                                    <div class="tab-content pt-2">
                                          <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                                <h5 class="card-title">Profile and vital signs</h5>
                                                <div class="row">
                                                      <div class="col-lg-3 col-md-4 label ">Patient</div>
                                                      <div class="col-lg-9 col-md-8" id="patient_pf"></div>
                                                </div>
                                                <div class="row">
                                                      <div class="col-lg-3 col-md-4 label">Address</div>
                                                      <div class="col-lg-9 col-md-8" id="address"></div>
                                                </div>
                                                <div class="row">
                                                      <div class="col-lg-3 col-md-4 label">Age</div>
                                                      <div class="col-lg-9 col-md-8" id="patient_age"></div>
                                                </div>
                                                <div class="row">
                                                      <div class="col-lg-3 col-md-4 label">Blood Pressure</div>
                                                      <div class="col-lg-9 col-md-8" id="blood_pressure"></div>
                                                </div>
                                                <div class="row">
                                                      <div class="col-lg-3 col-md-4 label">Pulse Rate</div>
                                                      <div class="col-lg-9 col-md-8" id="pulse_rate"></div>
                                                </div>
                                                <div class="row">
                                                      <div class="col-lg-3 col-md-4 label">Respiration Rate</div>
                                                      <div class="col-lg-9 col-md-8" id="resp_rate"></div>
                                                </div>
                                                <div class="row">
                                                      <div class="col-lg-3 col-md-4 label">Weight</div>
                                                      <div class="col-lg-9 col-md-8" id="weight"></div>
                                                </div>
                                                <div class="row">
                                                      <div class="col-lg-3 col-md-4 label">Height</div>
                                                      <div class="col-lg-9 col-md-8" id="height"></div>
                                                </div>
                                                <div class="row">
                                                      <div class="col-lg-3 col-md-4 label">BMI</div>
                                                      <div class="col-lg-9 col-md-8" id="bmi_value"></div>
                                                </div>
                                                <div class="row">
                                                      <div class="col-lg-3 col-md-4 label">Temperature</div>
                                                      <div class="col-lg-9 col-md-8" id="temeperature"></div>
                                                </div>
                                          </div>

                                          <div class="tab-pane fade profile-edit pt-3" id="chief-complaints">

                                                <div class="col-md-12 border rounded px-3 py-2 mb-3">
                                                      <h5 class="card-title">Chief complaints <i class="text-danger">*</i></h5>
                                                      <div class="col-md-12 mb-1">
                                                            <input type="text" name="search_complaint" id="search_complaint" placeholder="Filter to add complaint" class="form-control" />
                                                            <div id="complaint_result" class="list-group mb-2"></div>
                                                      </div>
                                                      <div id="client_complaint">
                                                      </div>
                                                </div>


                                                <div class="col-md-12 border rounded px-3 mb-2" id="comp_history">
                                                      <h5 class="card-title"> Amplifications</h5>
                                                      <form method="post" id="comp_history_form" class="row" action="javascript:void(0);">
                                                            <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
                                                            <!-- <input type="text" name="duration" id="duration"  /> -->
                                                            <div class="col-md-12 mb-2">
                                                                  <select name="complaint" id="complaint" class="form-select"></select>
                                                            </div>
                                                            <div class="col-md-12 mb-1">
                                                                  <textarea name="history" id="history" class="form-control"></textarea>
                                                            </div>
                                                            <div class="col-md-12 d-grid gap-2">
                                                              <button id="complaintSubmitBtn" class="btn btn-primary" type="submit">Submit amplifications</button>
                                                        </div>
                                                  </form>
                                                  <ul class="list-group my-2" id="complaint_history">
                                                  </ul>
                                            </div>

                                            <div class="col-md-12 border rounded py-2 mb-3" id="phy_exam">
                                                <h5 class="card-title mx-3"> Physical examination</h5>
                                                <form method="post" id="phy_exam_form" class="mx-3" action="javascript:void(0);">
                                                      <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
                                                      <div class="quill-editor-default" id="quillArea" style="height: 70px;"></div>
                                                      <textarea name="exam_text" style="display:none" id="hiddenArea"></textarea>
                                                      <div class="d-grid gap-2"> <button id="examSubmitBtn" type="submit" class="btn btn-primary mt-1">Submit physical examination</button>
                                                      </div>
                                                </form>
                                          </div>
                                    </div>

                                    <div class="tab-pane fade pt-3" id="lab-investigations"> <?php if(empty($categories)){ echo 'No results';} else { ?>
                                          <form method="post" action="javascript:void(0);" id="investigationForm">
                                                <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />

                                                <div class="accordion row mx-1" id="investigation_categories">
                                                      <?php foreach ($categories as $value) { ?>
                                                      <div class="accordion-item col-md-6 investigation-block" id="accordion_<?php echo $value->icat_token; ?>">
                                                            <h2 class="accordion-header" id="flush-headingOne<?php echo $value->icat_id; ?>">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne<?php echo $value->icat_id; ?>" aria-expanded="false" aria-controls="flush-collapseOne<?php echo $value->icat_id; ?>"> <div class="form-check"><input class="form-check-input parent" name='parent' id="parent-<?php echo $value->icat_id; ?>" type="checkbox"><label class="form-check-label" for="parent-<?php echo $value->icat_id; ?>"><?php echo $value->icat_name; ?><?php echo empty($value->icat_alias) ? '':' <code>('.$value->icat_alias.')</code>'; ?> <span id="check-indictor"></span></label></div>
                                                            </button>
                                                            </h2>

                                                            <div id="flush-collapseOne<?php echo $value->icat_id;?>" class="accordion-collapse collapse" aria-labelledby="flush-headingOne<?php echo $value->icat_id; ?>" data-bs-parent="#investigation_categories">
                                                            <div class="accordion-body">
                                                                  <ul class="list-group">
                                                                        <?php foreach ($subcategories as $sub){ if($sub->isub_category == $value->icat_token) { ?>
                                                                        <li class="list-group-item form-check" id="inv_subcat_<?php echo $sub->isub_token;?>">

                                                                        <div class="form-check">
                                                                              <input value="<?php echo $sub->isub_token; ?>" class="form-check-input child" name='investigation_ids' type="checkbox" id="inv_cat_<?php echo $sub->isub_token;?>">
                                                                              <label class="form-check-label" for="inv_cat_<?php echo $sub->isub_token;?>"><?php echo $sub->isub_name; ?><?php echo empty($sub->isub_alias) ? '':' <code>('.$sub->isub_alias.')</code>'; ?></label>
                                                                        </div>
                                                                        <span class="my-inv"></span>
                                                                  </li>
                                                                  <?php }}?>
                                                                  </ul>
                                                            </div>
                                                            </div>
                                                      </div>
                                                <?php } ?>
                                          </div>
                                          <div class="col-lg-12 mt-2 text-left">
                                                <button id="investigationSubmitBtn" type="submit" class="btn btn-primary">Save Changes</button>
                                          </div>
                                    </form>
                              <?php } ?>
                        </div>

                        <div class="tab-pane fade pt-3" id="disease-section">
                              <div class="col-md-12">
                                    <h5 class="card-title">Diseases</h5>
                                    <div class="mb-2" id="sickness-selection">
                                          <input type="text" name="search_disease" id="search_disease" placeholder="Filter to add disease" class="form-control" />
                                          <div id="disease_result" class="list-group mb-2"></div>
                                    </div>
                                    <ol class="list-group list-group-numbered" id="client_disease"></ol>
                              </div>
                        </div>

                        <div class="tab-pane fade pt-3" id="medications-section">
                              <div class="col-md-12">
                                    <h5 class="card-title">Medications</h5>
                                    <div class="mb-2" id="medicine-selection">
                                          <input type="text" name="search_medicine" id="search_medicine" placeholder="Filter to add medicine" class="form-control" />
                                          <div id="medicine_result" class="list-group mb-2"></div>
                                    </div>
                                    <ol class="list-group list-group-numbered" id="client_medicines"></ol>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</div>
</div>
</section>
<?php $this->load->view('templates/base_footer.php'); ?>
<script type="text/javascript">
      $(function() {

            $('input[type=checkbox].parent').change(function(){
                  var parent_id = $(this).closest('.accordion-item').attr('id');
                  var is_parent_checked = $(this).is(':checked');
                  if(is_parent_checked){
                        $(`#${parent_id} input[name="investigation_ids"]`).each(function() { 
                        this.checked = true;
                  });
                  } else {
                        $(`#${parent_id} input[name="investigation_ids"]`).each(function() {
                              this.checked = false;
                        });
                  }
            });

            $('input[type=checkbox].child').change(function(){
                  // if is checked
                  if(this.checked){
                        // check all children
                        var lenchk = $(this).closest('ul').find(':checkbox');
                        var lenchkChecked = $(this).closest('ul').find(':checkbox:checked');

                        //if all siblings are checked, check its parent checkbox
                        if (lenchk.length == lenchkChecked.length) {
                              $(this).closest('.accordion-item').find('input[type=checkbox].parent').prop('checked', true);
                        }else{
                              $(this).closest('.accordion-item').find('input[type=checkbox].parent').prop('checked', false);
                        }
                  } else {
                        // uncheck all children
                        // $(this).closest('.checkbox').find(':checkbox').prop('checked', false);
                        // $(this).closest('ul').siblings().find(':checkbox').prop('checked', false);
                        $(this).closest('.accordion-item').find('input[type=checkbox].parent').prop('checked', false);
                        }
                  });

            // On page load, retrieve patient on session
            get_session();
            function get_session(){
                  $.ajax({
                        type: "GET",  
                        url: "<?php echo base_url('doctor/get-full-session-info/').$my_session; ?>",  
                        data: "{}",
                        dataType: 'json',
                        success: function (response){
                              if(response.status){
                                    var data = response.data;
                                    var complaints = data.complaints;
                                    var diseases = data.diseases
                                    var medicines = data.medicines
                                    var inv_results = data.inv_results
                                    
                                    const name = data.pat_mname == null ? data.pat_fname+' '+data.pat_lname : data.pat_fname+' '+data.pat_mname+' '+data.pat_lname
                                    $("div#profile-overview div#patient_pf").html(`<code>${ name + ' ('+data.vs_record_patient_pf + ')'}</code>`);
                                    $("div#profile-overview div#address").html(`<code>${data.pat_address}</code>`);
                                    $("div#profile-overview div#patient_age").html(`<code>${data.pat_dob+' year(s)'}</code>`);
                                    $("div#profile-overview div#blood_pressure").html(`<code>${data.rec_blood_pressure+' mmHg'}</code>`);
                                    $("div#profile-overview div#pulse_rate").html(`<code>${data.rec_pulse_rate+' bpm'}</code>`);
                                    $("div#profile-overview div#resp_rate").html(`<code>${data.rec_respiration+' bpm'}</code>`);
                                    $("div#profile-overview div#weight").html(`<code>${data.rec_weight+' kg'}</code>`);
                                    $("div#profile-overview div#height").html(`<code>${data.rec_height+' cm'}</code>`);
                                    $("div#profile-overview div#bmi_value").html(`<code>${data.bmi}</code>`);
                                    $("div#profile-overview div#temeperature").html(`<code>${data.rec_temeperature+' &deg;C'}</code>`);                  
                                    $("div#phy_exam div#quillArea div.ql-editor").html("");
                                    $("div#phy_exam div#quillArea div.ql-editor").html(`<code>${data.sy_descriptions}</code>`);
                                    
                                    $('div#session_ids input#session_record_id').val(data.sy_record_id);
                                    $('div#session_ids input#session_symptom_id').val(data.sy_id);
                                    $('div#session_ids input#session_visit_id').val(data.vs_id);
                                    
                                    $('div#client_complaint').text("");
                                    $('ol#client_disease').text("");
                                    $('ol#client_medicines').text("");
                                    $('ul#complaint_history').text("");

                                    $('div#comp_history select#complaint').html('<option value="" selected>---Select complaint---</option>');
                                    if(complaints.length > 0){
                                          var str = '<li class="list-group-item">'
                                          $.each(complaints, function(key, value) {

                                                if(value.history) {
                                                      $('ul#complaint_history').append(`
                                                            <li class="list-group-item"><code>${value.text + ' [' + value.duration + ']'}: </code>${value.history}</li>
                                                            `);
                                                }

                                                if(!value.history) {
                                                      $('div#comp_history select#complaint').append(`
                                                            <option value="${value.token}">${value.text}</option>
                                                            `);
                                                }

                                                // $('div#comp_history_form input#duration').val(value.duration);
                                                // console.log(value.duration)
                                                
                                                str += `<a href="#" type="button" class="btn btn-outline-primary mb-1" title="Tap to delete" name="removeClientComplaintLink" data-id="${value.token}" data-title="${value.text}"><i class="bi bi-x-circle text-danger"></i> ${value.text + ' <code>[' + value.duration + ']</code>'}</a> `;  
                                          });
                                          str += '</li>';
                                          $("div#client_complaint").append(str);
                                    }
                                    if(diseases.length > 0){
                                          $.each(diseases, function(key, value) {
                                                $("ol#client_disease").append(`
                                                      <a class="list-group-item list-group-item-action fw-bold" href="#" name="removeClientDiseaseLink"
                                                      data-id="${value.code}" data-title="${value.text}">${value.text} (<code>${value.code}</code>)</a>
                                                      `);
                                          });
                                    }
                                    if(medicines.length > 0) {
                                          $.each(medicines, function(key, value) {
                                                var descInput = `
                                                <div class="row submitDescForm">
                                                <div class="col-lg-10">
                                                <input type="text" id="doctor_description${value.token}" 
                                                class="form-control form-control-sm" placeholder="Enter further description">
                                                </div>
                                                <div class="col-lg-2">
                                                <button data-id="${value.token}" id="submitDescBtn${value.token}" class="btn btn-primary btn-sm submitDescBtn"> Save </button>
                                                </div>
                                                </div>
                                                `;
                                                $("ol#client_medicines").append(`
                                                      <li class="list-group-item">
                                                      <a class="fw-bold" href="#" name="removeClientMedicineLink"
                                                      data-id="${value.token}" data-title="${value.text}">${value.text+' | <code>Category: </code>'+value.category+', <code>Format: </code>'+value.format+', <code>Unit: </code>'+value.title}
                                                      </a><div>
                                                      ${value.doctor_desc=='null' ? descInput : '<code>Descriptions: </code><small>'+value.doctor_desc+'</small>'}
                                                      </div></li>
                                                      `);
                                          });
                                    }

                                    if(inv_results.length > 0) {
                                          $.each(inv_results, function(key, value) {
                                                const inv_res = value.split("~");
                                                const input_id = 'input#inv_cat_'+inv_res[0];

                                                $(input_id).prop('checked', true);
                                                var lenchk = $(input_id).closest('ul').find(':checkbox');
                                                var lenchkChecked = $(input_id).closest('ul').find(':checkbox:checked');

                                                if(lenchkChecked.length > 0){
                                                      $(input_id).closest('.accordion-item').find('span#check-indictor').html('<i class="bi bi-check2-circle text-danger"></i>');
                                                }

                                                if (lenchk.length == lenchkChecked.length) {
                                                      $(input_id).closest('.accordion-item').find('input[type=checkbox].parent').prop('checked', true);
                                                }else{
                                                      $(input_id).closest('.accordion-item').find('input[type=checkbox].parent').prop('checked', false);
                                                }

                                                if(inv_res[1] !== 'null') {
                                                      $(input_id).attr('disabled', 'disabled');
                                                      $(input_id).closest('.accordion-item').find('input[type=checkbox].parent').attr('disabled', 'disabled');
                                                      const string = inv_res[1].split("&&");

                                                      let text = string[0];
                                                      text = text.replace("@text:", "");
                                                      text = text.replaceAll("$$$", " ");
                                                      let file = string[1];
                                                      file = file.replace("@file:", "");

                                                      const textSpan = 'li#inv_subcat_'+inv_res[0]+' span.my-inv';
                                                      $(textSpan).html("");
                                                      $(textSpan).append('<span class="fst-italic"> Results</span>: '+text);

                                                      if(file != 'null'){
                                                            const path = '<?php echo base_url('download/investigation-file/');?>'+file;
                                                            $(textSpan).append(`
                                                                  | <span class="fst-italic"> File </span>: <a target="_blank" href="${path}">
                                                                  Download </a>`);
                                                      }
                                                }
                                          });
                                    }

                                    const patient_state = data.vs_visit;
                                    if(patient_state == 'nipo_daktari_2' || patient_state == 'nipo_daktari_2r') {
                                          $("#complaintSubmitBtn").addClass('disabled');
                                          $("#examSubmitBtn").addClass('disabled');
                                          $("#investigationSubmitBtn").addClass('disabled');

                                          $('.investigation-block').each(function() {
                                                var currentEl = $(this).attr('id');
                                                var selected = [];
                                                $(`#${currentEl} input:checked`).each(function() {
                                                      selected.push($(this).attr('name'));
                                                });
                                                if(selected.length === 0){
                                                      $(`#${currentEl}`).hide();
                                                }
                                                $(`#${currentEl} input:checkbox:not(:checked)`).closest("li").hide();
                                          });
                                    }
                              }
                        }  
                  });
}

// Search complaint
$('#search_complaint').keyup(function() {
      $('#complaint_result').html("");
      var search = $(this).val();
      if(search){
            $.ajax({
                  url:"<?php echo base_url('complaints/search');?>",
                  method:"POST",
                  data:{query:search},
                  dataType: 'json',
                  delay: 250,
                  success:function(data)
                  {
                        $('#complaint_result').text("");
                        if(data.length > 0){
                              $.each(data, function(key, value) {
                                    $("#complaint_result").append(`
                                          <a href="#" name="saveComplaintLink" data-id="${value.token}" data-title="${value.text}" class="list-group-item list-group-item-action">
                                          ${value.text}</a>
                                          `);
                              });
                        }
                        else{
                              $('#complaint_result').html('<li class="list-group-item text-danger">No results!</li>');
                        }
                  }
            });
      }else{
            $('#complaint_result').html("");
      }
});


// Save the selected complaint
$("div#complaint_result").on('click', 'a', function() {
      var search = $('#search_complaint').val("");
      var complaint_token = $(this).attr('data-id');
      var complaint_name = $(this).attr('data-title');
      var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
      var symptom_id = $('div#session_ids').find('input[name="session_symptom_id"]').val();
      var visit_id = $('div#session_ids').find('input[name="session_visit_id"]').val();
      if(this.name == "saveComplaintLink") {
            bootbox.prompt({
                  title:'You have selected <code>'+complaint_name+'</code>',
                  inputType: "text",
                  placeholder: "Duration",
                  buttons: {
                        confirm: {label: '<i class="fa fa-check"></i> Agree',className: "btn-success",},
                        cancel: {label: '<i class="fa fa-times"></i> Disagree',className: "btn-danger",},
                  },
                  callback: function (result) 
                  {
                        // alert(result)
                        if (result !== "undefined" && result !== null && result !== '')
                        {
                              var dialog = bootbox.dialog({
                                    message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                                    closeButton: false,
                              }).on("shown.bs.modal", function () {
                                    $.ajax({
                                          url: '<?php echo base_url("doctor/save-patient-complaint/"); ?>'+symptom_id+"/"+record_id+"/"+visit_id,
                                          type: "POST",
                                          dataType: "json",
                                          data: { complaint: complaint_token, duration: result},
                                          success: function (response) {
                                                $('#complaint_result').text("");
                                                if (response.status) {
                                                      bootbox.alert(response.data.toString(), function () {
                                                            get_session();
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
                        }else{
                              $('div#complaint_result').text("");
                        }
                  }
            });
      }
});


// Delete the selected chief complaint
$("div#client_complaint").on('click', 'a', function() {
      var comp_token = $(this).attr('data-id');
      var comp_name = $(this).attr('data-title');
      var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
      var symptom_id = $('div#session_ids').find('input[name="session_symptom_id"]').val();
      var visit_id = $('div#session_ids').find('input[name="session_visit_id"]').val();
      if(this.name == "removeClientComplaintLink") {
            bootbox.confirm({
                  message: 'You have selected to remove <code>"'+comp_name+'"</code> complaint',
                  buttons: 
                  {
                        confirm: { label: '<i class="fa fa-check"></i> Agree',className: "btn-success",},
                        cancel: { label: '<i class="fa fa-times"></i> Disagree',className: "btn-danger",},
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
                                          url: '<?php echo base_url("doctor/delete-patient-complaint/"); ?>'+symptom_id+"/"+record_id+"/"+visit_id,
                                          type: "POST",
                                          dataType: "json",
                                          data: { complaint: comp_token },
                                          success: function (response) {
                                                if (response.status) {
                                                      bootbox.alert(response.data.toString(), function () {
                                                            get_session();
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

// Save amplifications
$("#comp_history_form").validate({
      errorPlacement: function(error, element) {
          error.addClass('text-danger');
          error.insertAfter(element);
    },
    debug: false,
    errorClass: "is-invalid",
    validClass: "is-valid",
    errorElement: "div",
    rules: { 
          complaint: { required: true },
          history: { required: true, minlength: 1, maxlength: 300 },
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
              var formdata = $("#comp_history_form").serialize();
              var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
              var symptom_id = $('div#session_ids').find('input[name="session_symptom_id"]').val();
              var visit_id = $('div#session_ids').find('input[name="session_visit_id"]').val();
              $.ajax({
                  url: '<?php echo base_url('doctor/save-complaint-history/'); ?>'+symptom_id+'/'+record_id+'/'+visit_id,
                  type: "POST",
                  data: formdata,
                  dataType: "JSON",
                  success: function (response) {
                      if (response.status) {
                          $("#comp_history_form")[0].reset();
                          bootbox.alert(response.data.toString(), function () {
                              get_session();
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


// Save physical examination
$("#phy_exam_form").on("submit",function() {
      var editor_data = $("div#phy_exam div#quillArea div.ql-editor").html();
      $("div#phy_exam textarea#hiddenArea").val(editor_data);

      var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
      var symptom_id = $('div#session_ids').find('input[name="session_symptom_id"]').val();
      var visit_id = $('div#session_ids').find('input[name="session_visit_id"]').val();

      var text = $('div#phy_exam').find('textarea[name="exam_text"]').val();
      if(text != '<p><br></p>'){
            var dialog = bootbox.dialog({
                  message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                  closeButton: false,
            })
            .on("shown.bs.modal", function () {
                  $.ajax({
                        type: "POST",  
                        url: "<?php echo base_url('doctor/update-sypmtoms/'); ?>"+symptom_id+"/"+record_id,  
                        data: { exam_text: text },
                        dataType: 'json',
                        success: function (response) {
                              if (response.status) {
                                    bootbox.alert(response.data.toString(), function () {
                                          get_session();
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
});


// Submit investigations
$("#investigationForm").on("submit", function() {
      var valuesArray = [];
      $("input:checkbox[name=investigation_ids]:checked").each(function(){
            valuesArray.push($(this).val());
      });
      if(valuesArray.length === 0){                      
      } else {
            // console.log(valuesArray);
            // return;

            var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
            var symptom_id = $('div#session_ids').find('input[name="session_symptom_id"]').val();
            var visit_id = $('div#session_ids').find('input[name="session_visit_id"]').val();
            var dialog = bootbox.dialog({
                  message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                  closeButton: false,
            }).on("shown.bs.modal", function () {
                  $.ajax({
                        type: "POST",  
                        url: "<?php echo base_url('doctor/update-investigations/'); ?>"+symptom_id+"/"+record_id+"/"+visit_id,  
                        data: { investigation_ids: valuesArray },
                        dataType: 'json',
                        success: function (response) {
                              if (response.status) {
                                    bootbox.alert(response.data.toString(), function () {
                                          // get_session();
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
                              bootbox.alert(errorThrown.toString(), function () {
                                    dialog.modal("hide");
                              });
                        },
                  });
            });
      }
});

// Search disease
$('#search_disease').keyup(function() {
      $('#disease_result').html("");
      var search = $(this).val();
      if(search){
            $.ajax({
                  url:'<?php echo base_url("disease/search");?>',
                  method:"POST",
                  data:{ query: search },
                  dataType: 'json',
                  delay: 250,
                  success:function(data)
                  {
                        $('#disease_result').text("");
                        if(data.length > 0){
                              $.each(data, function(key, value) {
                                    $("#disease_result").append(`
                                          <a href="#" name="saveDiseaseLink" data-id="${value.code}" 
                                          data-title="${value.text}" class="list-group-item list-group-item-action">
                                          ${value.text} - ${value.code}</a>
                                          `);
                              });
                        }
                        else{
                              $('#disease_result').html('<li class="list-group-item text-danger">No results!</li>');
                        }
                  }
            });
      }else{
            $('#disease_result').html("");
      }
});

// Save the selected disease
$("div#disease_result").on('click', 'a', function() {
      var search = $('#search_disease').val("");
      var disease_id = $(this).attr('data-id');
      var disease_name = $(this).attr('data-title');
      var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
      var symptom_id = $('div#session_ids').find('input[name="session_symptom_id"]').val();
      var visit_id = $('div#session_ids').find('input[name="session_visit_id"]').val();
      if(this.name == "saveDiseaseLink") {
            bootbox.confirm({
                  message:'You selected <code>'+disease_name+'</code>',
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
                                          url: '<?php echo base_url("doctor/save-patient-disease/"); ?>'+symptom_id+"/"+record_id+"/"+visit_id,
                                          type: "POST",
                                          dataType: "json",
                                          data: { disease: disease_id },
                                          success: function (response) {
                                                $('#disease_result').text("");
                                                if (response.status) {
                                                      bootbox.alert(response.data.toString(), function () {
                                                            get_session();
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


// Delete the selected client disease
$("ol#client_disease").on('click', 'a', function() {
      var disease_id = $(this).attr('data-id');
      var disease_name = $(this).attr('data-title');
      var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
      var symptom_id = $('div#session_ids').find('input[name="session_symptom_id"]').val();
      var visit_id = $('div#session_ids').find('input[name="session_visit_id"]').val();
      if(this.name == "removeClientDiseaseLink") {
            bootbox.confirm({
                  message:'You have selected to remove <code>'+disease_name+'</code>',
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
                                          url: '<?php echo base_url("doctor/delete-myclient-disease/"); ?>'+symptom_id+"/"+record_id+"/"+visit_id,
                                          type: "POST",
                                          dataType: "json",
                                          data: { disease: disease_id },
                                          success: function (response) {
                                                if (response.status) {
                                                      bootbox.alert(response.data.toString(), function () {
                                                            get_session();
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

 // Search medicine
 $('div#medicine-selection input#search_medicine').keyup(function() {
      $('#medicine_result').html("");
      var search = $(this).val();
      if(search){
            $.ajax({
                  url:"<?php echo base_url('doctor/search-medicines');?>",
                  method:"POST",
                  data:{ query: search },
                  dataType: 'json',
                  delay: 250,
                  success:function(data)
                  {
                        if(data.length > 0){
                              $.each(data, function(key, value) {
                                    var str = '';
                                    if(value.id == "10000001") {
                                          str = value.name + ' (' + value.short + ')';
                                    } else {
                                          str = value.name + ' (' + value.short + ') | ' + value.text + ' | ' +value.format + ' <span class="badge bg-primary badge-number">' + value.available + '</span>';
                                    }
                                    $("#medicine_result").append(`
                                          <a href="#" name="saveMedicineLink" data-id="${value.id}" data-title="${value.name+' ('+value.text+')'}" class="list-group-item list-group-item-action">${str}</a>
                                          `);
                              });
                        }
                        else {
                              $('#medicine_result').html('<li class="list-group-item text-danger">No results!</li>');
                        }
                  }
            });
      }
});

 // Save the selected medicine
 $("div#medicine_result").on('click', 'a', function() {
      var search = $('#search_medicine').val("");
      var medicine_id = $(this).attr('data-id');
      var medicine_name = $(this).attr('data-title');
      var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
      var symptom_id = $('div#session_ids').find('input[name="session_symptom_id"]').val();
      var visit_id = $('div#session_ids').find('input[name="session_visit_id"]').val();
      if(this.name == "saveMedicineLink") {
            bootbox.confirm({
                  message:'Select <code>'+medicine_name+'</code>?',
                  buttons: 
                  {
                        confirm: {label: '<i class="fa fa-check"></i> Agree',className: "btn-success",},
                        cancel: {label: '<i class="fa fa-times"></i> Disagree',className: "btn-danger",},
                  },
                  callback: function (result) 
                  {
                        if (result == true)
                        {
                              $('div#medicine_result').text("");
                              var dialog = bootbox.dialog({
                                    message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                                    closeButton: false,
                              }).on("shown.bs.modal", function () {
                                    $.ajax({
                                          url: '<?php echo base_url("doctor/save-patient-medicine/"); ?>'+symptom_id+"/"+record_id+"/"+visit_id,
                                          type: "POST",
                                          dataType: "json",
                                          data: { stock: medicine_id },
                                          success: function (response) {
                                                if (response.status) {
                                                      bootbox.alert(response.data.toString(), function () {
                                                            get_session();
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

// Submit doctor description on client medicine in stock
$("ol#client_medicines").on('click', 'button', function() {
      var id = $(this).attr('id');
      var stock = $(this).attr('data-id');

      var text = $('div.submitDescForm input#doctor_description'+stock).val();

      if(text) {
            var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
            var symptom_id = $('div#session_ids').find('input[name="session_symptom_id"]').val();
            var visit_id = $('div#session_ids').find('input[name="session_visit_id"]').val();

            var dialog = bootbox.dialog({
                  message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Saving ...</div>',
                  closeButton: false,
            }).on("shown.bs.modal", function () {
                  $.ajax({
                        url: '<?php echo base_url("doctor/save-patient-medicine-description/"); ?>'+symptom_id+"/"+record_id+"/"+visit_id,
                        type: "POST",
                        dataType: "json",
                        data: { string: text, stock: stock },
                        success: function (response) {
                              if (response.status) {
                                    bootbox.alert(response.data.toString(), function () {
                                          get_session();
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
});

// Delete the client medicine
$("ol#client_medicines").on('click', 'a', function() {
      var stock = $(this).attr('data-id');
      var medicine_name = $(this).attr('data-title');

      var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
      var symptom_id = $('div#session_ids').find('input[name="session_symptom_id"]').val();
      var visit_id = $('div#session_ids').find('input[name="session_visit_id"]').val();
      if(this.name == "removeClientMedicineLink") {
            bootbox.confirm({
                  message:'You have selected to remove <code>'+medicine_name+'</code>',
                  buttons: 
                  {
                        confirm: { label: '<i class="fa fa-check"></i> Agree',className: "btn-success",},
                        cancel: { label: '<i class="fa fa-times"></i> Disagree',className: "btn-danger",},
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
                                          url: '<?php echo base_url("doctor/delete-myclient-medicine/"); ?>'+symptom_id+"/"+record_id+"/"+visit_id,
                                          type: "POST",
                                          dataType: "json",
                                          data: { stock: stock },
                                          success: function (response) {
                                                if (response.status) {
                                                      bootbox.alert(response.data.toString(), function () {
                                                            get_session();
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


 // Release patient
 $("a#release-patient").on("click", function(e) {
      var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
      var link = '<?php echo base_url('doctor/release-patient/'); ?>'+record_id;

      e.preventDefault();

      bootbox.confirm({
            title: '<code>' + $('#patient_pf').text() + '</code>',
            message: "Do you want to release this patient now? This action cannot be undone.",
            buttons: {
                  cancel: {
                        label: '<i class="fa fa-times"></i> Cancel'
                  },
                  confirm: {
                        label: '<i class="fa fa-check"></i> Confirm'
                  }
            },
            callback: function (result) {
                  if(result == true) window.location.href = link;
            }
      });
});

 // Set-in-patient - observation
 $("a#set-in-patient").on("click", function(e) {
      var record_id = $('div#session_ids').find('input[name="session_record_id"]').val();
      var link = '<?php echo base_url('doctor/set-in-patient/'); ?>'+record_id;

      e.preventDefault();

      bootbox.confirm({
            title: '<code>' + $('#patient_pf').text() + '</code>',
            message: "Do you really want to put this patient under observation?",
            buttons: {
                  cancel: {
                        label: '<i class="fa fa-times"></i> No'
                  },
                  confirm: {
                        label: '<i class="fa fa-check"></i> Yes'
                  }
            },
            callback: function (result) {
                  if(result == true) window.location.href = link;
            }
      });
});


});
</script>
