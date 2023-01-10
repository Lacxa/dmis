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
            </div><?php } ?>

            <div class="col-12">
            <div class="card">
                  <div class="card-body">
                        <h5 class="card-title">
                              Select the relevant report from the below selection
                        </h5>
                        <div class="row g-3">
                              <div class="col-12">
                                    <select id="inputState" class="form-select">
                                          <option value="" selected>Choose...</option>
                                          <option value="1">Patient report</option>
                                    </select>
                              </div>
                        </div>
                        <span id="form-area">

                              <div id="performance_report_row" class="mt-4" style="display: none;">
                                    <form class="row g-3" method="post" action="<?php echo base_url('reports/performance/lab');?>" id="performance-report-form">
                                          <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />

                                          <div class="col-lg-4 col-md-4">
                                                <div class="container">
                                                      <div class="datepicker input-group date">
                                                            <input type="text" class="form-control" placeholder="Choose a start-date" name="start" id="start" autoComplete="off" required />
                                                            <span class="input-group-append">
                                                                  <span class="input-group-text bg-light d-block"><i class="bi bi-calendar3"></i>
                                                                  </span>
                                                            </span>
                                                      </div>
                                                </div>
                                          </div>

                                          <div class="col-lg-4 col-md-4">
                                                <div class="container">
                                                      <div class="datepicker input-group date">
                                                            <input type="text" class="form-control" placeholder="Choose a end-date" name="end" id="end" autoComplete="off" required />
                                                            <span class="input-group-append">
                                                                  <span class="input-group-text bg-light d-block"><i class="bi bi-calendar3"></i>
                                                                  </span>
                                                            </span>
                                                      </div>
                                                </div>
                                          </div>

                                          <div class="col-lg-4 col-md-4">
                                                <button type="reset" class="btn btn-secondary"> Reset
                                                </button>
                                                <button type="submit" class="btn btn-primary"> Search
                                                </button>
                                          </div>
                                    </form>
                              </div>

                        </span>
                  </div>
            </div>
      </div>

</div>
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

      // Get selected option and display form
      $('select').on('change', function() {
            const value = this.value;
            $('#performance_report_row').hide();
            if(value){
                  if(value == 1){
                        $('#performance_report_row').show();
                  }
            }
      });


});
</script>
