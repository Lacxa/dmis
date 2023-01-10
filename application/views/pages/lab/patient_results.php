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
          
          <div class="accordion accordion-flush" id="accordionFlushExample">
            <?php if(!empty($diagnostics)) { $i = 0; foreach ($diagnostics as $key => $value) { $i++;?>
            <div class="accordion-item">
                  <h2 class="accordion-header" id="flush-headingOne-<?php echo $i;?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne-<?php echo $i;?>" aria-expanded="false" aria-controls="flush-collapseOne-<?php echo $i;?>">
                      <?php echo strtoupper($key);?>
                    </button>
                  </h2>
                  <div id="flush-collapseOne-<?php echo $i;?>" class="accordion-collapse collapse" aria-labelledby="flush-headingOne-<?php echo $i;?>" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                      <ul class="list-group">
            <?php foreach($value as $subkey => $row) {
              if(empty($row['results'])) { ?>

                <li class="list-group-item">
                  <form method="post" class="investigation" action="javascript:void(0);" id="investigation_form_<?php echo $row['token'];?>" enctype="multipart/form-data">

                    <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />

                    <div class="ms-2 me-auto">
                      <div class="fw-bold mb-2"><?php echo '<code>' . strtoupper($row['name']) . '</code>';?>                       
                      </div>

                      <div class="row g-3">

                        <div class="col-md-4" style="display:none;">
                          <input type="number" name="investigation" value="<?php echo $row['token'];?>" class="form-control" required/>
                        </div>

                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" class="form-control" name="results" placeholder="Enter results here" />
                            <span class="input-group-text"><?php echo empty($row['alias']) ? 'N':$row['alias'];?></span>
                          </div>
                        </div>

                        <div class="col-md-4">
                          <input name="file" type="file" class="form-control investigation-<?php echo $row['token'];?>" accept=".pdf, .jpg, .jpeg, .png" />
                        </div>

                        <div class="col-md-2">
                          <button data-catId="<?php echo $row['token'];?>" name="saveBtn" class="btn btn-primary sub-<?php echo $row['token'];?>">Save Changes</button>
                        </div>
                      </div>

                    </div>
                  </form>
                </li>
              <?php } else { ?>
                <li class="list-group-item">
                  <div class="ms-2 me-auto">
                      <div class="fw-bold mb-2"><?php echo '<code>' . strtoupper($row['name']).'</code>';?>                    
                      </div>

                      <div class="row g-3">
                        
                      <div class="<?php echo empty($row['results']['file']) ? 'col-md-12':'col-md-8';?>">
                        <span class="fst-italic">Result: </span><?php echo $row['results']['text'];?> <?php echo empty($row['alias']) ? '(N)':'('.$row['alias'].')';?>
                      </div>

                      <?php if(!empty($row['results']['file'])) { ?>                        
                      <div class="col-md-4">
                        <span class="fst-italic"> File: </span>
                        <a target="_blank" href="<?php echo base_url('download/investigation-file/'.$row['results']['file']);?>"> Download
                        </a>
                      </div>
                      <?php } ?>
                        
                      </div>
                    </div>
                </li>

                <?php }} ?>
            </ul>
          </div>
                  </div>
                </div>
                <?php }}else{echo 'No any investigations specified by doctor';}?>
          </div>
          </div>
        </div>
      </div>

    </div>
  </section>
  <?php $this->load->view('templates/base_footer.php'); ?>

  <script type="text/javascript">
    $(function() {

      // Submit investigation data
      $("form.investigation button").click( function() {
        var form = $(this).parents('form:first');
        var form_id = form.attr('id');

        var form_data = $('#'+form_id)[0];
        var data = new FormData(form_data);

        var dialog = bootbox.dialog({
          message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait while processing your request...</div>',
          closeButton: false,
        }).on("shown.bs.modal", function () {
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
                location.reload();
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