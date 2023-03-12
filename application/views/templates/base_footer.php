</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
  <div class="copyright">
    &copy; Copyright 2022-<?php echo date("Y") ?> <strong><span><a href="https://nit.ac.tz/" target="_blank">National Institute of Transport</a></span></strong>. All Rights Reserved
  </div>
  <div class="credits">
    Developed by <a target="_blank" href="https://nit.ac.tz/index.php/mis-2/">NIT MIS DEPARTMENT</a>
  </div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- JQUERY JS File -->
<script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>

<!-- BOOTSTRAP DATEPICKER JS File -->
<script src="<?php echo base_url('assets/js/bootstrap-datepicker.min.js'); ?>"></script>

<!-- Vendor JS Files -->
<script src="<?php echo base_url('assets/vendor/apexcharts/apexcharts.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/chart.js/chart.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/echarts/echarts.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/quill/quill.min.js'); ?>"></script>
<!-- <script src="<f?php echo base_url('assets/vendor/simple-datatables/simple-datatables.js'); ?>"></script> -->
  <script src="<?php echo base_url('assets/vendor/tinymce/tinymce.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/php-email-form/validate.js'); ?>"></script>

  <!-- Data table entry -->
  <!-- <script src="<f?php echo base_url('assets/js/datatable/dataTables.min.js');?>"></script> -->
  <script src="<?php echo base_url('assets/js/datatable/jquery.dataTables.min.js');?>"></script>
  <!-- <script src="<f?php echo base_url('assets/js/datatable/dataTables.bootstrap4.min.js') ;?>"></script> -->
  <script src="<?php echo base_url('assets/js/datatable/dataTables.bootstrap5.min.js') ;?>"></script>
  <script src="<?php echo base_url('assets/js/datatable/dataTables.buttons.min.js');?>"></script>
  <script src="<?php echo base_url('assets/js/datatable/jszip.min.js');?>"></script>
  <script src="<?php echo base_url('assets/js/datatable/pdfmake.min.js');?>"></script>
  <script src="<?php echo base_url('assets/js/datatable/vfs_fonts.js');?>"></script>
  <script src="<?php echo base_url('assets/js/datatable/buttons.html5.min.js');?>"></script>
  <script src="<?php echo base_url('assets/js/datatable/buttons.print.min.js');?>"></script>
  <script src="<?php echo base_url('assets/js/datatable/dataTables.responsive.min.js');?>"></script>
  <script src="<?php echo base_url('assets/js/datatable/responsive.bootstrap.min.js');?>"></script>

  <!-- Bootbox JS File -->
  <script src="<?php echo base_url('assets/js/bootbox/bootbox.all.js'); ?>"></script>

  <!-- jquery validate library -->
  <script src="<?php echo base_url('assets/js/jquery.validate.js');?>"></script>
  <script src="<?php echo base_url('assets/js/jquery.validate.additional-methods.min.js');?>"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
  
  <script type="text/javascript">
    $(function() {
      
      $.ajaxSetup({
        data: {
          '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'
        }
      });
      
      <?php if($this->session->userdata('user_role') == 'MO') { ?>
        function session_counter(){
          $.ajax({
            url: "<?php echo base_url('doctor/ajax-count-session-patients'); ?>",
            type: "POST",
            dataType: "json",
            success: function (response) {
              if(response.status){
                const res = response.data;
                if(res > 0){
                  $('.session-counter').text(res);
                }else{
                  $('.session-counter').text("");
                }
              }
            }
          });
        }

        session_counter();
        setInterval(function(){
          session_counter();
        }, 6000);
      <?php } else if($this->session->userdata('user_role') == 'PH') { ?>
        function prescription_counter(){
          $.ajax({
            url: "<?php echo base_url('pharmacy/ajax-count-prescription'); ?>",
            type: "POST",
            dataType: "json",
            success: function (response) {
              if(response.status){
                const res = response.data;
                if(res > 0){
                  $('.prescription-counter').text(res);
                }else{
                  $('.prescription-counter').text("");
                }
              }
            }
          });
        }

        prescription_counter();
        setInterval(function(){
          prescription_counter();
        }, 7000);        
      <?php } else if($this->session->userdata('user_role') == 'LAB') { ?>
        function lab_patients_counter(){
          $.ajax({
            url: "<?php echo base_url('lab/ajax-count-patients'); ?>",
            type: "POST",
            dataType: "json",
            success: function (response) {
              if(response.status){
                const res = response.data;
                if(res > 0){
                  $('.patients-counter').text(res);
                }else{
                  $('.patients-counter').text("");
                }
              }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                  // console.log(jqXHR);
            }
          });
        }

        lab_patients_counter();
        setInterval(function(){
          lab_patients_counter();
        }, 7000);        
      <?php } ?>
    });
  </script>

</body>

</html>