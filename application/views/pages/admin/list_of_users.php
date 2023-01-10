<?php $this->load->view('templates/base_header.php');
$csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
);
?>
<section class="section">
  <div class="row">


    <div class="col-lg-12">
      <div class="collapse" id="addUserCollapse">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"> User/Employee registration form</h5>

            <form class="row g-3" method="post" action="javascript:void(0);" id="reg_form">
              <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
              <div class="col-md-12">
                <input type="text" name="file_number" class="form-control" placeholder="Personal File Number"/>
              </div>
              <div class="col-md-4">
                <input type="text" name="fname" class="form-control" placeholder="First Name"/>
              </div>
              <div class="col-md-4">
                <input type="text" name="mname" class="form-control" placeholder="Middle Name"/>
              </div>
              <div class="col-md-4">
                <input type="text" name="lname" class="form-control" placeholder="Last Name"/>
              </div>
              <div class="col-md-4">
                <input type="email" name="email" class="form-control" placeholder="Email" />
              </div>
              <div class="col-md-4">
                <select name="category" id="inputState" class="form-select">
                  <option value="" selected> ---Select Employee Title--- </option>
                  <?php foreach($categories as $row){ ?>
                    <option value="<?php echo $row['cat_id']; ?>">
                      <?php echo $row['cat_name'] .' (' .$row['cat_alias']. ')'; ?> </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <select name="role" id="role" class="form-select">
                    <option value="" selected> ---Select Employee Role--- </option>
                    <?php foreach($roles as $row){ ?>
                      <option value="<?php echo $row['role_id']; ?>"><?php echo $row['role_name']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <input type="text" name="phone" class="form-control numberonly" placeholder="Phone" maxlength="10" minlength="10" />
                </div>
                <div class="col-md-6">
                  <input type="text" name="password" class="form-control" placeholder="Password" />
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <button type="reset" class="btn btn-secondary"> Reset </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>


      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title">List of users</h5>
              <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addUserCollapse"><i class="bi bi-plus-circle me-1"></i> Add User
              </button>
            </div>

            <table id="table_users" class="table table-striped table-sm nowrap" style="width:100%">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Full Name</th>
                  <th scope="col">File Number</th>
                  <th scope="col">Title</th>
                  <th scope="col">Role</th>
                  <th scope="col">State</th>
                  <th scope="col">Action</th>
                  <th scope="col">Incharge</th>
                  <th scope="col">Email</th>
                  <th scope="col">Mobile</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>

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

      const title = 'Administrator section | List of Users';
      var table_users = $('#table_users').DataTable({
        oLanguage: {
          sProcessing: "loading...",
          sLengthMenu: 'Show <select class="form-select">'+
          '<option value="10">10</option>'+
          '<option value="20">20</option>'+
          '<option value="50">50</option>'+
          '<option value="100">100</option>'+
          '<option value="-1">All</option>'+
          '</select> records'
        },
        responsive: true,
        "processing":true,
        "serverSide":true, 
        "order":[],
        "ajax": {
          url : "<?php echo base_url('admin/users'); ?>",
          type : 'POST'
        },
        "ordering": false,
        "dom": 'Blfrtip',
        "buttons": [
        { extend: "copy", title: title,},
        { extend: "excel", title: title, },
        { extend: "csv", title: title, },
        { extend: "pdf", title: title},
        { extend: "print", title: title, },
        ],
      });

  
  $("#table_users tbody").on('click', 'button', function() {
   var id = $(this).attr('data-id');
   var pf = $(this).attr('data-pf');

   if(this.name == "activateButton") {
     bootbox.confirm({
      message: `Activate a user with PF number <code>${pf}</code>?`,
      buttons: {
        confirm: {
          label: '<i class="fa fa-check"></i> Yes',
          className: "btn-success",
        },
        cancel: {
          label: '<i class="fa fa-times"></i> No',
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {

         var dialog = bootbox
         .dialog({
          message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
          closeButton: false,
        }).on("shown.bs.modal", function () {
          $.ajax({
            url: '<?php echo base_url('admin/user-state/1/');?>'+id,
            type: "POST",
            data: {},
            dataType: "JSON",
            success: function (response) {
              bootbox.alert(response.data.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
            error: function (jqXHR, textStatus, errorThrown) {
              bootbox.alert(errorThrown.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
          });
        });

      }
    }
  });
   } else if(this.name == "deactivateButton") {
     bootbox.confirm({
      message: `Disable a user with PF number <code>${pf}</code>?`,
      buttons: {
        confirm: {
          label: '<i class="fa fa-check"></i> Yes',
          className: "btn-success",
        },
        cancel: {
          label: '<i class="fa fa-times"></i> No',
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {

         var dialog = bootbox
         .dialog({
          message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
          closeButton: false,
        }).on("shown.bs.modal", function () {
          $.ajax({
            url: '<?php echo base_url('admin/user-state/0/');?>'+id,
            type: "POST",
            data: {},
            dataType: "JSON",
            success: function (response) {
              bootbox.alert(response.data.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
            error: function (jqXHR, textStatus, errorThrown) {
              bootbox.alert(errorThrown.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
          });
        });

      }
    }
  });
   } else if(this.name == "DelButton") {
     bootbox.confirm({
      message: `Remove a user with PF number <code>${pf}</code>?`,
      buttons: {
        confirm: {
          label: '<i class="fa fa-check"></i> Yes',
          className: "btn-success",
        },
        cancel: {
          label: '<i class="fa fa-times"></i> No',
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {

         var dialog = bootbox
         .dialog({
          message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
          closeButton: false,
        }).on("shown.bs.modal", function () {
          $.ajax({
            url: '<?php echo base_url('admin/user/delete/');?>'+id,
            type: "POST",
            data: {},
            dataType: "JSON",
            success: function (response) {
              bootbox.alert(response.data.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
            error: function (jqXHR, textStatus, errorThrown) {
              bootbox.alert(errorThrown.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
          });
        });
      }
    }
  });
   } else if(this.name == "enableInchargeButton") {
     bootbox.confirm({
      message: `Make incharge a user with PF number <code>${pf}</code>?`,
      buttons: {
        confirm: {
          label: '<i class="fa fa-check"></i> Yes',
          className: "btn-success",
        },
        cancel: {
          label: '<i class="fa fa-times"></i> No',
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {
         var dialog = bootbox
         .dialog({
          message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
          closeButton: false,
        }).on("shown.bs.modal", function () {
          $.ajax({
            url: '<?php echo base_url('admin/user-incharge/1/');?>'+id,
            type: "POST",
            data: {},
            dataType: "JSON",
            success: function (response) {
              bootbox.alert(response.data.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
            error: function (jqXHR, textStatus, errorThrown) {
              bootbox.alert(errorThrown.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
          });
        });
      }
    }
  });
   } else if(this.name == "disableInchargeButton") {
     bootbox.confirm({
      message: `Discharge a user with PF number <code>${pf}</code>?`,
      buttons: {
        confirm: {
          label: '<i class="fa fa-check"></i> Yes',
          className: "btn-success",
        },
        cancel: {
          label: '<i class="fa fa-times"></i> No',
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {
         var dialog = bootbox
         .dialog({
          message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
          closeButton: false,
        }).on("shown.bs.modal", function () {
          $.ajax({
            url: '<?php echo base_url('admin/user-incharge/0/');?>'+id,
            type: "POST",
            data: {},
            dataType: "JSON",
            success: function (response) {
              bootbox.alert(response.data.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
            error: function (jqXHR, textStatus, errorThrown) {
              bootbox.alert(errorThrown.toString(), function () {
                table_users.ajax.reload();
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


// Submit registration form
$("#reg_form").validate({
  rules: { 
    file_number: { required: true },
    fname: { required: true },
    lname: { required: true },
    email: { required: true },
    category: { required: true },
    role: { required: true },
    phone: { required: true },
    password: { required: true },
  },
  messages: {},

  submitHandler: function () {
    var dialog = bootbox
    .dialog({
      message:
      '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Loading...</div>',
      closeButton: false,
    })
    .on("shown.bs.modal", function () {
      var formdata = $("#reg_form").serialize();
      $.ajax({
        url: '<?php echo base_url('admin/add-user');?>',
        type: "POST",
        data: formdata,
        dataType: "JSON",
        success: function (response) {
          table_users.ajax.reload();
          if (response.status) {
            $("#reg_form")[0].reset();
            $('.collapse').collapse('hide')
            bootbox.alert(response.data.toString(), function () {
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


$("#table_users tbody").on('change', 'select', function() {
  const action = this.name;

  var employee = $(this).attr('data-emp');
  var select_id = this.id;
  var value_id = $("#" + select_id + " option:selected" ).val();
  var value_text = $("#" + select_id + " option:selected" ).text();

  if(action == "changeCategory") {
    bootbox.confirm({
      message: `Change user category to <code>${value_text}</code>?`,
      buttons: {
        confirm: {
          label: '<i class="fa fa-check"></i> Agree',
          className: "btn-success",
        },
        cancel: {
          label: '<i class="fa fa-times"></i> Disagree',
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {
         var dialog = bootbox
         .dialog({
          message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
          closeButton: false,
        }).on("shown.bs.modal", function () {
          $.ajax({
            url: '<?php echo base_url('admin/user-change-category/');?>'+employee+'/'+value_id,
            type: "POST",
            data: {},
            dataType: "JSON",
            success: function (response) {
              bootbox.alert(response.data.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
            error: function (jqXHR, textStatus, errorThrown) {
              bootbox.alert(errorThrown.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
          });
        });
      }
    }
  });
  } else if(action == 'changeRole') {
    bootbox.confirm({
      message: `Change user role to <code>${value_text}</code>?`,
      buttons: {
        confirm: {
          label: '<i class="fa fa-check"></i> Agree',
          className: "btn-success",
        },
        cancel: {
          label: '<i class="fa fa-times"></i> Disagree',
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {
         var dialog = bootbox
         .dialog({
          message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
          closeButton: false,
        }).on("shown.bs.modal", function () {
          $.ajax({
            url: '<?php echo base_url('admin/user-change-role/');?>'+employee+'/'+value_id,
            type: "POST",
            data: {},
            dataType: "JSON",
            success: function (response) {
              bootbox.alert(response.data.toString(), function () {
                table_users.ajax.reload();
                dialog.modal("hide");
              });
            },
            error: function (jqXHR, textStatus, errorThrown) {
              bootbox.alert(errorThrown.toString(), function () {
                table_users.ajax.reload();
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


});
</script>