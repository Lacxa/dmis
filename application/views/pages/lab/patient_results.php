<?php $this->load->view('templates/base_header.php');
$error = $this->session->flashdata('error');
$success = $this->session->flashdata('success');
$color = isset($success) ? 'primary' : 'danger';
$message = isset($success) ? $success : $error;

$csrf = array(
  'name' => $this->security->get_csrf_token_name(),
  'hash' => $this->security->get_csrf_hash()
);
?>
<section class="section">
  <div class="row">

    <?php if (validation_errors() != '') { ?>
      <div class="col-12">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo validation_errors();?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>
    <?php } ?>

    <?php if(isset($success) || isset($error)) { ?>
      <div class="col-12">
        <div class="alert alert-<?php echo $color;?> alert-dismissible fade show" role="alert">
          <?php echo $message;?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>
    <?php } ?>

    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Lab investigations</h4>
            <div>
              <a type="button" href="<?php echo base_url('lab/return-patient/').$patient['rec_id'];?>" class="btn btn-danger" id="return-patient">
              <i class="bi bi-backspace me-1"></i>
              </a>
              <a type="button" href="<?php echo base_url('lab/set-in-patient/').$patient['rec_id'];?>" class="btn btn-danger" id="set-in-patient">
                <i class="bi bi-exclamation-circle me-1"></i>Set In-Patient
              </a>
              <a type="button" href="<?php echo base_url('lab/release-patient/').$patient['rec_id'];?>" class="btn btn-primary" id="release-patient">
                <i class="bi bi-person-check me-1"></i> Release
              </a>
            </div>
          </div>
          <span id="diagnostics_span"></span>
        </div>
      </div>
    </div>
  </div>
</section>
  <?php $this->load->view('templates/base_footer.php'); ?>

  <script type="text/javascript">
    $(function() {

      get_diagnostics();

      function get_diagnostics() {
      $.ajax({
        type: "GET",  
        url: '<?php echo base_url('lab/patient-results-get-ajax/'.$patient['rec_id']);?>',  
        data: "{}",
        dataType: 'json',
        success: function (resp) {
          setDiagnostics(resp.data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          // console.log(jqXHR)
        },
      });     
    }

    function setDiagnostics(data){
      var html = '';
      if(data.length == 0){
        html += '<div class="alert alert-warning alert-dismissible fade show" role="alert"><i class="bi bi-info-circle me-1"></i>Oops!, no any investigations specified by doctor</div>';
      }else{
        
        var headerArray = [];
        $('#diagnosticsAccordion .collapse').each(function(){
          const elementID = this.id;
          if($(`#${elementID}`).hasClass("show")){
            headerArray.push(elementID);
          }
        });

        html += '<div class="accordion accordion-flush" id="diagnosticsAccordion">';
        var i = 0;
        $.each(data, function(key, value){
          i++;
          html += '<div class="accordion-item">';
          html += `<h2 class="accordion-header" id="flush-heading-${i}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-${i}" aria-expanded="false" aria-controls="flush-collapse-${i}">${key.toUpperCase()}
                    </button>
                  </h2>`;
          html += `<div id="flush-collapse-${i}" class="accordion-collapse collapse" aria-labelledby="flush-heading-${i}" data-bs-parent="#diagnosticsAccordion">`;
          html += '<div class="accordion-body"><ul class="list-group">';
          $.each(value, function(subKey, subValue){
            html += '<li class="list-group-item">';
            if(subValue.results){
              html += '<div class="ms-2 me-auto">';
              html += `<div class="fw-bold mb-2"><code>${subValue.name.toUpperCase()}</code></div>`;
              html += `<div class="row g-3"><div class="col-md-${subValue.results.file ? '4':'8'}"><span class="fst-italic">Result: </span>${subValue.results.text} (${subValue.alias ? subValue.alias:'N'})</div>`;
              if(subValue.results.file){
                html += '<div class="col-md-4">';
                html += '<span class="fst-italic"> File: </span>';
                html += `<a target="_blank" href="<?php echo base_url('download/investigation-file/');?>${subValue.results.file}"> View </a>`;
                html += '</div>';
              }
              html += '<div class="col-md-4">';
              html += `<a name="resetLink" type="button" data-token="${subValue.token}" class="btn btn-outline-danger" href="#"> Reset Results</a>`;
              html += '</div>';

              html += '</div>';
            }else{
              html += `<form method="post" class="investigation" action="javascript:void(0);" id="investigation_form_${subValue.token}" enctype="multipart/form-data">`;
              html += `<input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />`;
              html += '<div class="ms-2 me-auto">';
              html += `<div class="fw-bold mb-2"><code>${subValue.name.toUpperCase()}</code></div>`;
              html += '<div class="row g-3">';
              html +=  `<div class="col-md-4" style="display:none;"><input type="number" name="investigation" value="${subValue.token}" class="form-control" required/></div>`;
              html += `<div class="col-md-6"><div class="input-group"><input type="text" class="form-control" name="results" placeholder="Enter results here" /><span class="input-group-text">${subValue.alias ? subValue.alias:'N'}</span></div></div>`;
              html += `<div class="col-md-4"><input name="file" type="file" class="form-control investigation-${subValue.token}" accept=".pdf, .jpg, .jpeg, .png" /></div>`;
              html += `<div class="col-md-2"><button data-catId="${subValue.token}" name="saveBtn" class="btn btn-primary sub-${subValue.token}">Save Changes</button></div>`;
              html += '</div>';
              html += '</div>';
              html += '</form>';
            }
            html += '</li>';
          });
          html += '</ul></div>';
          html += '</div>';
          html += '</div>';
        });
      }

      $("span#diagnostics_span").html(html);

      $.each(headerArray, function(index, item){
        $(`#${item}`).addClass('show');
        // $(`#${item}`).collapse('show');
      });
    }

    $("span#diagnostics_span").on('click', 'a', function(e) {
      e.preventDefault();
      if(this.name == 'resetLink') {
        const token = $(this).attr('data-token');
        var dialog = bootbox.dialog({
          message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',closeButton: false,}).on("shown.bs.modal", function () {
            $.ajax({  
              url: '<?php echo base_url('lab/reset-patient-results/'.$patient['rec_id']);?>',
              type: "POST",
              dataType: "json",
              data: { token: token },
              timeout: 10000,
              success: function (response) {
                if(response.status){
                  dialog.modal("hide");
                  // console.log(response);
                  get_diagnostics();
                } else {
                  bootbox.alert(response.data.toString(), function () {
                    dialog.modal("hide");
                  });
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
                bootbox.alert(errorThrown.toString(), function () {
                  dialog.modal("hide");
                  // console.log(jqXHR);
                });
              },
            });
          });
        }
      });

    // Submit investigation data
    $("span#diagnostics_span").on('click', 'form.investigation button', function() {
      const btnType = this.name;
      if(btnType == 'saveBtn'){
        var form = $(this).parents('form:first');
        var form_id = form.attr('id');

        var form_data = $('#'+form_id)[0];
        var data = new FormData(form_data);

        bootbox.confirm({
          message: '<code> Are you sure? </code>',
          buttons: {confirm: {label: '<i class="fa fa-check"></i> Yes',className: "btn-success",},cancel: {label: '<i class="fa fa-times"></i> No',className: "btn-danger",},},
          callback: function (result) {
            if (result == true) {
              var dialog = bootbox.dialog({
                message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait while processing your request...</div>',
                closeButton: false,}).on("shown.bs.modal", function () {
                  $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',  
                    url: '<?php echo base_url('lab/patient-results-post/'.$patient['rec_id']);?>',  
                    data: data,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    timeout: 600000,
                    success: function (response) {
                      if(response.status){
                        dialog.modal("hide");
                        get_diagnostics();
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


    $("a#release-patient").on("click", function(e) {
      var link = this;

        e.preventDefault();

        bootbox.confirm({
          title: "<?php echo '<code>'.$subHeading.'</code>';?>",
          message: "Do you want to release this client now? This cannot be undone.",
          buttons: {
            cancel: {
              label: '<i class="fa fa-times"></i> Cancel'
            },
            confirm: {
              label: '<i class="fa fa-check"></i> Confirm'
            }
          },
          callback: function (result) {
            if(result == true) window.location = link.href;
          }
        });
      });


      $("a#return-patient").on("click", function(e) {
        var link = this;
        e.preventDefault();

        bootbox.confirm({
          title: "<?php echo '<code>'.$subHeading.'</code>';?>",
          message: "Do you want to return this patient to a doctor? This action cannot be undone. Remember to copy the already filled-in results!",
          buttons: {
            cancel: {
              label: '<i class="fa fa-times"></i> Cancel'
            },
            confirm: {
              label: '<i class="fa fa-check"></i> Confirm'
            }
          },
          callback: function (result) {
            if(result == true) window.location = link.href;
          }
        });
      });

      // Set-in-patient - observation
      $("a#set-in-patient").on("click", function(e) {
        var link = this;
        e.preventDefault();

        bootbox.confirm({
          title: "<?php echo '<code>'.$subHeading.'</code>';?>",
          message: "Do you really want to put this patient under observation?",
          buttons: {
            cancel: { label: '<i class="fa fa-times"></i> No'},
            confirm: { label: '<i class="fa fa-check"></i> Yes' }
              },
              callback: function (result) {
                    if(result == true) window.location.href = link;
              }
            });
      });




    })


  </script>