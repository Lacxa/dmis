<?php 
$this->load->view('templates/base_header.php'); 
$error = $this->session->flashdata('error');
$success = $this->session->flashdata('success');
$color = isset($success) ? 'primary' : 'danger';
$message = isset($success) ? $success : $error;
$csrf = array('name' => $this->security->get_csrf_token_name(), 'hash' => $this->security->get_csrf_hash());
?>

<section class="section">
      <div class="row"><?php if(validation_errors()!=''){?>
            <div class="col-12">
                  <div class="alert alert-danger alert-dismissible fade show" role="alert"> <?php echo validation_errors();?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                  </div> <?php } if(isset($success) || isset($error)){?>
                   <div class="col-12">
                        <div class="alert alert-<?php echo $color;?> alert-dismissible fade show" role="alert"><?php echo $message;?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
            </div>
      <?php } ?>

      <div class="col-12">
            <div class="card">
                  <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">List of editable patients</h5>
                  </div>
                  <table id="editable_patients" class="table table-striped table-sm nowrap" style="width:100%">
                      <thead>
                          <tr>
                              <th scope="col">#</th>
                              <th scope="col">Patient</th>
                              <th scope="col">File Number</th>
                              <th scope="col">Address</th>
                              <th scope="col">Gender</th>
                              <th scope="col">Time</th>
                              <th scope="col">Action</th>
                        </tr>
                  </thead>
                  <tbody></tbody>
            </table>
      </div>
</div>
</div>

<div class="modal fade" id="patientLabEditsModal" tabindex="-1">
      <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                  <form method="post" action="javascript:void(0);" id="investigationForm">
                        <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
                        <div class="modal-header">
                            <h5 class="modal-title">EDIT INVESTIGATIONS</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row g-3">
                              <div class="col-md-6" style="display: none;">
                                    <div class="input-group mb-3">
                                          <span class="input-group-text" id="basic-addon1">ID</span>
                                          <input type="number" id="record_id" name="record_id" class="form-control" aria-label="Record ID" aria-describedby="basic-addon1">
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
                              <span id="investigation-list"></span>
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

<div class="row" id="history-area"></div>
</section>

<?php $this->load->view('templates/base_footer.php'); ?>

<script type="text/javascript">
      $(function() {            


          const title = 'List of editable patients';
          var editable_patients = $('#editable_patients').DataTable({
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
                  url : "<?php echo base_url('doctor/edit-investigations');?>",
                  type : 'POST'
            },
            "ordering": false,
            "dom": 'Blfrtip',
            "buttons": [
            { extend: "copy", title: title,},
            { extend: "excel", title: title,},
            { extend: "csv", title: title,},
            { extend: "pdf", title: title,},
            { extend: "print", title: title,},
            ],
      });


          $("#editable_patients tbody").on('click', 'button', function() {
            var id = $(this).attr('data-id');
            var name = $(this).attr('data-name');
            var file_no = $(this).attr('data-pf');

            if(this.name == "editBtn"){
                  var dialog = bootbox.dialog({
                        message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                        closeButton: false,}).on("shown.bs.modal", function () {
                              $.ajax({
                                    url: '<?php echo base_url('doctor/get-record-investigations/');?>'+id,
                                    type: "POST",
                                    data: {},
                                    dataType: "JSON",
                                    success: function (response) {
                                          if(response.status) {
                                                const data = response.data;
                                                editable_patients.ajax.reload();
                                                $('#patientLabEditsModal').modal('show').on('shown.bs.modal', function () {
                                                      $("form#investigationForm input#record_id").val(id);
                                                      $("form#investigationForm input#patient_name").val(name);
                                                      $("form#investigationForm input#patient_file").val(file_no);
                                                      $('form#investigationForm input#patient_file').prop('readonly', true);
                                                });

                                                var str = '<div class="accordion row mx-1" id="investigation_categories">';

                                                $.each(data.categories, function(key, value){
                                                      str += `<div class="accordion-item col-md-6 investigation-block" id="accordion_${value.icat_id}">`;
                                                      str += `<h2 class="accordion-header" id="flush-headingOne${value.icat_id}"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne${value.icat_id}" aria-expanded="false" aria-controls="flush-collapseOne${value.icat_id}">${value.icat_name}${value.icat_alias ? ' ('+value.icat_alias+')' : ''}</button></h2>`;
                                                      str += `<div id="flush-collapseOne${value.icat_id}" class="accordion-collapse collapse" aria-labelledby="flush-headingOne${value.icat_id}" data-bs-parent="#investigation_categories">`;
                                                      str += `<div class="accordion-body">`;
                                                      str += `<ul class="list-group">`;

                                                      $.each(data.subcategories, function(key1, val1){
                                                            if(val1.isub_category == value.icat_token){
                                                                  var checked = false;
                                                                  if(jQuery.inArray(val1.isub_token, data.posted) !== -1){
                                                                        checked = true;
                                                                  }
                                                                  str += `<li class="list-group-item form-check" id="inv_subcat_${val1.isub_token}">`;
                                                                  str += `<div class="form-check">`;
                                                                  str += `<input value="${val1.isub_token}" class="form-check-input" name='investigation_ids' type="checkbox" id="inv_cat_${val1.isub_token}" ${checked ? 'checked':''}/>`;
                                                                  str += `<label class="form-check-label" for="changesMade">${val1.isub_name}${val1.icat_alias ? ' <code>('+val1.icat_alias+')</code>' : ''}</label>`;
                                                                  str += '</div>';
                                                                  str += `</li>`;
                                                            }
                                                      });

                                                      str += '</ul>';
                                                      str += '</div>';
                                                      str += '</div>';
                                                      str += '</div>';
                                                });


                                                str += '</div>';
                                                $("span#investigation-list").html(str);

                                                dialog.modal("hide");
                                                // console.log(response.data);
                                          } else {
                                                bootbox.alert(response.data, function () {
                                                      editable_patients.ajax.reload();
                                                      dialog.modal("hide");
                                                });
                                          }
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                          bootbox.alert(errorThrown.toString(), function () {
                                                editable_patients.ajax.reload();
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
            var record_id = $('div#patientLabEditsModal').find('input[name="record_id"]').val();
            var dialog = bootbox.dialog({
                  message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                  closeButton: false,
            }).on("shown.bs.modal", function () {
                  $.ajax({
                        type: "POST",  
                        url: "<?php echo base_url('doctor/post-update-investigations/'); ?>"+record_id,  
                        data: { investigation_ids: valuesArray },
                        dataType: 'json',
                        success: function (response) {
                              if (response.status) {
                                    bootbox.alert(response.data.toString(), function () {
                                          editable_patients.ajax.reload();
                                          $('div#patientLabEditsModal').modal('hide');
                                          dialog.modal("hide");
                                    });
                              } else {
                                    bootbox.alert(response.data.toString(), function () {
                                          editable_patients.ajax.reload();
                                          dialog.modal("hide");
                                    });
                              }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                              bootbox.alert(errorThrown.toString(), function () {
                                    editable_patients.ajax.reload();
                                    dialog.modal("hide");
                              });
                        },
                  });
            });
      }
});

    });
</script>
