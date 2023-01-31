<?php
$this->load->view('templates/base_header.php');

$csrf = array(
  'name' => $this->security->get_csrf_token_name(),
  'hash' => $this->security->get_csrf_hash()
);

$type = $action == 1 ? 'New user' : 'Expired/Triggered';
$a_message1 = 'For security, you are immediately prompted to change the factory-default password for the your account.';
$a_message2 = 'Your password may have expired, or you have triggered this action.';
$a_message = $action == 1 ? $a_message1 : $a_message2;
?>

<section class="section dashboard">
  <div class="row">
    <div class="col-12">
			<div class="card recent-sales overflow-auto">
				<div class="card-body">
					<h5 class="card-title">
						Change Password <span>| <?php echo $type;?></span>
					</h5>
          <p class="small fst-italic"><?php echo $a_message; ?> </p>
          <ul>
            <li>The password must have at least six (6) characters in length and not exceeding thirty-two (32) characters</li>
            <li>The password must have at least one uppercase and lowercase letter</li>
            <li>The password must have at least one number</li>
            <li>The password must have at least one special character <code>('!@#$%^&*()\-_=+{};:,<.>§~')</code></li>
          </ul>
          <form id="change-password" action="javascript:void(0);">
            <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
            
            <div class="row mb-3">
              <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
              <div class="col-md-8 col-lg-9">
                <div class="input-group has-validation">
                  <input name="password" type="password" class="form-control" id="currentPassword">
                  <span class="input-group-text password-showhide" id="inputGroupPrepend" style="cursor: pointer;">
                    <i class="show-password bi bi-eye"></i>
                    <i class="hide-password bi bi-eye-slash" style="display: none;"></i>
                  </span>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
              <div class="col-md-8 col-lg-9">                         
                <div class="input-group has-validation">
                    <input name="newpassword" type="password" class="form-control" id="newPassword">
                    <span class="input-group-text password-showhide" id="inputGroupPrepend" style="cursor: pointer;">
                        <i class="show-password bi bi-eye"></i>
                        <i class="hide-password bi bi-eye-slash" style="display: none;"></i>
                    </span>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
              <div class="col-md-8 col-lg-9">                                         
                <div class="input-group has-validation">
                  <input name="renewpassword" type="password" class="form-control" id="renewPassword">
                  <span class="input-group-text password-showhide" id="inputGroupPrepend" style="cursor: pointer;">
                  <i class="show-password bi bi-eye"></i>
                        <i class="hide-password bi bi-eye-slash" style="display: none;"></i>
                    </span>
                </div>
              </div>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php $this->load->view('templates/base_footer.php'); ?>

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
    
    $("form#change-password").validate({
      errorPlacement: function(error, element) {
        error.addClass('text-danger');
        error.insertAfter(element);
      },
      debug: false,
      errorClass: "is-invalid",
      validClass: "is-valid",
      errorElement: "div",
      rules: { 
        password: { required: true },
        newpassword: { required: true },
        renewpassword: { required: true },
      },
      highlight: function( element, errorClass, validClass ) {
        $(element).addClass(errorClass).removeClass(validClass);
      },
      unhighlight: function( element, errorClass, validClass ) {
        $(element).removeClass(errorClass).addClass(validClass);
      },
      submitHandler: function () {
        var dialog = bootbox.dialog({message:
          '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',closeButton: false,
        }).on("shown.bs.modal", function () {
          var formdata = $("form#change-password").serialize();
          $.ajax({
            url: '<?php echo base_url('password/change/'.$action.'/'.@$header);?>',
            type: "POST",
            data: formdata,
            dataType: "JSON",
            success: function (response) {
              if (response.status) {
                $("#change-password")[0].reset();
                const action = '<?php echo $action;?>';
                bootbox.alert(response.data.toString(), function () {
                  dialog.modal("hide");
                  if(action == '1'){
                    const base_url = '<?php echo base_url(); ?>';
                    window.location.href = base_url;
                  }
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


  });
</script>
