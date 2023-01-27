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
                        <h5 class="card-title">
                              Patient history search area
                        </h5>

                        <div class="row g-3">
                              <div class="col-12">
                                    <input type="text" id="search_client" name="search_client" placeholder="Enter keyword (eg. client file number, name, phone, NHIF card number)" class="form-control" required />
                                    <ul class="list-group mb-2" id="client_search_results"></ul>
                              </div>
                        </div>

                        <form class="row g-3 mt-2" method="post" action="javascript:void(0);" id="client-report-form">

                              <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />

                              <div class="col-md-3">
                                    <input type="text" placeholder=" File No:" class="form-control" autocomplete="off" name="pf" id="pf" required />
                              </div>

                              <div class="col-md-3">
                                    <div class="container">
                                          <div class="datepicker input-group date">
                                                <input type="text" class="form-control" placeholder="Select start date" name="start" id="start" autoComplete="off" required />
                                                <span class="input-group-append">
                                                      <span class="input-group-text bg-light d-block"><i class="bi bi-calendar3"></i>
                                                      </span>
                                                </span>
                                          </div>
                                    </div>
                              </div>

                              <div class="col-md-3">
                                    <div class="container">
                                          <div class="datepicker input-group date">
                                                <input type="text" class="form-control" placeholder="Select end date" name="end" id="end" autoComplete="off" required />
                                                <span class="input-group-append">
                                                      <span class="input-group-text bg-light d-block"><i class="bi bi-calendar3"></i>
                                                      </span>
                                                </span>
                                          </div>
                                    </div>
                              </div>

                              <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary"> Search </button>
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

            $('.datepicker').datepicker({
                  clearBtn: true,
                  autoclose: true,
                  changeMonth: true,
                  changeYear: true,
                  format: "yyyy-mm-dd",
            });

            // Search patient
            $('input#search_client').keyup(function() {
                  var search = $(this).val();
                  if(search){
                        $.ajax({
                              url:"<?php echo base_url('lab/search-patient');?>",
                              method:"POST",
                              data:{ search_keyword: search },
                              dataType: 'json',
                              delay: 250,
                              success:function(data)
                              {
                                    $('#client_search_results').html("");
                                    if(data.length > 0){
                                          $.each(data, function(key, value) {
                                                $("#client_search_results").append(`<a href="#" name="searchedPatientLink" data-pf="${value.pf}" class="list-group-item list-group-item-action">${value.first_name} ${value.middle_name == null ? '' : value.middle_name} ${value.last_name} | ${value.pf} | ${value.phone} | ${value.address} ${value.nhif == null ? '' : '| '+value.nhif}</a>`);
                                          });
                                    } else {
                                          $('#client_search_results').append('<li class="list-group-item text-danger"> Oops!, no results. </li>');
                                    }
                              }
                        });
                  } else {
                        $('#client_search_results').html("");
                  }
            });

      // Populate the client report form with the selected client
      $("ul#client_search_results").on('click', 'a', function() {
            if(this.name == "searchedPatientLink") {
                  var client_pf = $(this).attr('data-pf');
                  $('form#client-report-form input#pf').val(client_pf);
                  $('#client_search_results').html("");
            }
      });    


// Get history data
$("#client-report-form").validate({
      errorPlacement: function(error, element) {
            error.addClass('text-danger');
            if (element.attr("name") == "start" || element.attr("name") == "end") {
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
            pf: { required: true, minlength: 11, maxlength: 11 },
            start: { required: true },
            end: { required: true },
      },
      highlight: function( element, errorClass, validClass ) {
            $(element).addClass(errorClass).removeClass(validClass);
      },
      unhighlight: function( element, errorClass, validClass ) {
          $(element).removeClass(errorClass).addClass(validClass);
      },
      submitHandler: function () {
            $("div#history-area").html("");
            var dialog = bootbox.dialog({
                  message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                  closeButton: false,}).on("shown.bs.modal", function () {
                        var formdata = $("#client-report-form").serialize();
                        $.ajax({
                              url: '<?php echo base_url('lab/patient-history');?>',
                              type: "POST",
                              data: formdata,
                              dataType: "JSON",
                              success: function (response) {
                                    if (response.status) {
                                          // $("#client-report-form")[0].reset();
                                          dialog.modal("hide");
                                          const data = response.data;
                                          // console.log(data);
                                          const path = '<?php echo base_url('download/investigation-file/');?>';
                                          var str = '';
                                          $.each(data, function(key, value){
                                                str += '<div class="col-md-6"><div class="card"><div class="card-body">';
                                                str += '<h5 class="card-title">' + value.full_name + ' (' + value.pf + ') <span>| ' + value.day + '</span></h5>';
                                                str += '<ol class="list-group list-group-numbered">';
                                                $.each(value.diagnosis, function(key2, val){
                                                      str += '<li class="list-group-item">';
                                                      str += val.parent + ' | <em>' + val.name + '</em>: <code> Results: </code>' + val.results + ' (' + val.alias + ')'
                                                      str += val.file ? `, <code> File: </code><a target="_blank" href="${path+val.file}">View</a>` : '';
                                                      str += '</li>';
                                                });
                                                str += '</ol></div></div></div>';
                                          });
                                          $("div#history-area").html(str);

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
