<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$error = $this->session->flashdata('error');
$success = $this->session->flashdata('success');

$csrf = array(
  'name' => $this->security->get_csrf_token_name(),
  'hash' => $this->security->get_csrf_hash()
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo $title; ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url('assets/img/nit_favicon.png'); ?>">
  <link href="<?php echo base_url('assets/img/nit-apple-touch-icon.png');?>" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/boxicons/css/boxicons.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/quill/quill.snow.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/quill/quill.bubble.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/remixicon/remixicon.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/vendor/simple-datatables/style.css'); ?>" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="<?php echo base_url('');?>" class="logo d-flex align-items-center w-auto">
                  <img src="<?php echo base_url('assets/img/nit_logo.png'); ?>" alt="">
                  <span class="d-none d-lg-block">NIT DMIS</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <?php
                    echo isset($success) ? '<div class="alert alert-success" role="alert">'.$success.'</div>' : 
                    (isset($error) ? '<div class="alert alert-warning" role="alert">'.$error.'</div>' : '');
                    ?>
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your email & password to login</p>
                  </div>

                  <form class="row g-3 needs-validation" novalidate method="post" action="<?php echo base_url('home/auth');?>">

                    <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />

                    <div class="col-12">
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@&nbsp;</span>
                        <input type="email" name="email" value="<?php echo set_value('email'); ?>" class="form-control" id="email" required>
                        <div class="invalid-feedback">Please enter your email.</div>
                        <?php echo form_error('email'); ?>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="input-group has-validation">
                        <span class="input-group-text">&#128477;</span>
                        <input type="password" name="password" class="form-control" id="password" value="<?php echo set_value('password'); ?>" required>
                        <span class="input-group-text password-showhide" style="cursor: pointer;">
                          <i class="show-password bi bi-eye"></i>
                          <i class="hide-password bi bi-eye-slash" style="display: none;"></i>
                        </span>
                        <div class="invalid-feedback">Please enter your password.</div>
                        <?php echo form_error('password');?>
                      </div>
                    </div>

                    <div class="col-12">
                      <?php echo $captcha_image; ?>
                      <a class="btn btn-light" href="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <i class="bi bi-arrow-repeat"></i>
                      </a>
                    </div>

                    <div class="col-12">
                      <input type="text" name="captcha" class="form-control" placeholder="Enter the above captcha text" id="captchaText" autocomplete="off" required>
                      <div class="invalid-feedback">Please enter valid captcha text!</div>
                      <?php echo form_error('captcha'); ?>
                    </div>
                    
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Login</button>
                    </div>
                  </form>

                </div>
              </div>

              <!-- <div class="copyright text-center">
                &copy; Copyright 2022-<g?php echo date("Y") ?> <strong><span><a href="https://nit.ac.tz/" target="_blank">National Institute of Transport</a></span></strong>. All Rights Reserved
              </div>

              <div class="credits">
                Developed by <a target="_blank" href="https://nit.ac.tz/index.php/mis-2/">NIT MIS DEPARTMENT</a>
              </div> -->

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- JQUERY JS File -->
  <script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>

  <!-- Vendor JS Files -->
  <script src="<?php echo base_url('assets/vendor/apexcharts/apexcharts.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/chart.js/chart.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/echarts/echarts.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/quill/quill.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/simple-datatables/simple-datatables.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/tinymce/tinymce.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/vendor/php-email-form/validate.js'); ?>"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>

  <script type="text/javascript">
    $(function() {

      $(".show-password, .hide-password").on('click', function() {
        var passwordId = $(this).parents('div:first').find('input').attr('id');
        if ($(this).hasClass('show-password')) {
          $("#" + passwordId).attr("type", "text");
          $(this).parent().find(".show-password").hide();
          $(this).parent().find(".hide-password").show();
        } else {
          $("#" + passwordId).attr("type", "password");
          $(this).parent().find(".hide-password").hide();
          $(this).parent().find(".show-password").show();
        }
      });

    });
  </script>

</body>

</html>