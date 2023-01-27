<?php $this->load->view('templates/base_header.php'); ?>
<section class="section">
  <div class="row">

      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title">Patients monitor</h5>
            </div>

            <table id="table_monitor" class="table table-striped table-sm nowrap" style="width:100%">
              <thead>
                <tr>
                  <th scope="col">Patient</th>
                  <th scope="col">Reception</th>
                  <th scope="col">Doctor</th>
                  <th scope="col">Laboratory</th>
                  <th scope="col">Pharmacy</th>
                  <th scope="col">Entry Time</th>
                  <th scope="col">Address</th>
                  <th scope="col">Emergency</th>
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


      const title = 'Monitoring section';
      var table_monitor = $('#table_monitor').DataTable({
        oLanguage: {
          sProcessing: "",
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
          url : "<?php echo base_url('reports/monitor/'.@$header); ?>",
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
      
      setInterval(function() {
        table_monitor.ajax.reload();
      }, 30000);


      $("#table_users tbody").on('click', 'button', function() {
       var id = $(this).attr('data-id');
       var pf = $(this).attr('data-pf');
       if(this.name == "activateButton") {
         bootbox.confirm({
          message: `Activate a user with PF number ${pf}?`,
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
          message: `Disable a user with PF number ${pf}?`,
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
       }  else if(this.name == "DelButton"){
         bootbox.confirm({
          message: `Remove a user with PF number ${pf}?`,
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

});
</script>