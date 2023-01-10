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

    <?php if (validation_errors() != ''){?>
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
            <h4 class="card-title">Patient prescriptions</h4>
            <div>
              <a type="button" href="<?php echo base_url('pharmacy/return-patient/').$patient['rec_id'];?>" class="btn btn-danger" id="return-patient">
                <i class="bi bi-backspace me-1"></i>
              </a>
              <a type="button" href="<?php echo base_url('pharmacy/release-patient/').$patient['rec_id'];?>" class="btn btn-primary" id="release-patient">
                <i class="bi bi-person-check me-1"></i> Release
              </a>
            </div>
          </div>
          <?php foreach($prescriptions as $key => $row) { 
            $query = '+++';
            if(substr($row->text, 0, strlen($query)) === $query || $row->in == 0) {
              $text = str_replace($query,'', $row->text); ?>
              <ul class="list-group">
                <li class="list-group-item">
                  <?php if($row->in == 1) {
                    echo '<i class="bi bi-capsule me-1 text-primary"></i>';
                    echo $row->medicine_name. ' ('.$row->title. ' | '.$row->grand. ' | '.$row->parent.')'; echo $text == 'null' ? '' : ': '.$text;
                  } else {
                    echo '<i class="bi bi-capsule me-1 text-danger"></i>';
                    echo $text;
                  }
                  ?>
                </li>
              </ul>

            <?php } else { ?>
              <form class="row g-3 mt-1" method="post" id="presc-form-<?php echo $key;?>" action="<?php echo base_url('pharmacy/save-prescriptions/').$patient['rec_id'];?>">
                <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
                <input type="text" name="stock_id" value="<?php echo $row->id;?>" style="display: none;">
                <input type="text" name="token" value="<?php echo $row->token;?>" style="display: none;">
                <input type="text" name="patient" value="<?php echo $patient['rec_patient_file'];?>" style="display: none;">
                <?php $max = (int) $row->total - (int) $row->used; ?>
                <input type="number" name="i_max" value="<?php echo $max;?>" style="display: none;">

                <div class="col-md-2">
                  <select name="action" class="form-select action_input">
                    <option value="1" selected>Confirm</option>
                    <option value="2">Reject</option>
                  </select>
                </div>

                <div class="col-md-9 input-col-1">
                 <div class="input-group mb-3">
                  <span class="input-group-text"><?php echo $row->medicine_name. ' ('.$row->title. ' | '.$row->grand. ' | '.$row->parent.')'; echo $row->text == 'null' ? '' : ': '.$row->text; ?></span>
                  <input type="number" name="count" class="form-control numberonly" min="1" max="<?php echo $max;?>">
                  <span class="input-group-text"><?php echo $max;?></span>
                </div>
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-md-9 input-col-2" style="display: none;">
               <div class="input-group mb-3">
                <span class="input-group-text">#</span>
                <input name="text" type="text" value="<?php echo $row->medicine_name. ' ('.$row->title. ' | '.$row->grand. ' | '.$row->parent.'): '.$row->text; ?>" class="form-control" readonly>
              </div>
            </div>

            <div class="col-md-1">
              <button data-catId="" name="save-btn" class="btn btn-primary">Save</button>
            </div>
          </form>
        <?php } }?>

      </div>
    </div>
  </div>

</div>
</section>
<?php $this->load->view('templates/base_footer.php'); ?>

<script type="text/javascript">
  $(function() {

    $('.numberonly').keypress(function (e) {
      var charCode = (e.which) ? e.which : event.keyCode
      if (String.fromCharCode(charCode).match(/[^0-9]/g))
        return false;
    });

    $('select.action_input').on('change', function() { 
      const value = this.value;
      var form = $(this).closest('form')[0];
      var form_id = form.id;
      var form_action = form.action;

      var input_option_1 = 'form#'+ form_id +' div.input-col-1';
      var input_option_2 = 'form#'+ form_id +' div.input-col-2';     


      $(input_option_1).hide();
      $(input_option_2).hide();

      if(value) {
        if(value == 1) {
          $(input_option_1).show();
        } else if(value == 2) {
          $(input_option_2).show();
        }
      }
    });


    $("a#release-patient").on("click", function(e) {
      var link = this;

      e.preventDefault();

      bootbox.confirm({
        title: "<code><?php echo $subHeading;?></code>",
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
        title: "<code><?php echo $subHeading;?></code>",
        message: "Do you want to return this patient to the doctor? This action cannot be undone. Remember to copy the already filled-in results!",
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




  })


</script>