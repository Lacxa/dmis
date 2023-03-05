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

    <div class="clo-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Patients pool</h5>

          <!-- Default Tabs -->
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="reception-tab" data-bs-toggle="tab" data-bs-target="#reception" type="button" role="tab" aria-controls="reception" aria-selected="true">Reception
                <span id="done-reception-badge" class="badge bg-danger badge-number"></span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="lab-tab" data-bs-toggle="tab" data-bs-target="#lab" type="button" role="tab" aria-controls="lab" aria-selected="false">Lab
                <span id="done-lab-badge" class="badge bg-danger badge-number"></span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="lab-returns-tab" data-bs-toggle="tab" data-bs-target="#lab-returns" type="button" role="tab" aria-controls="lab-returns" aria-selected="false">Lab Returns 
                <span id="lab-returns-badge" class="badge bg-danger badge-number"></span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pharmacy-returns-tab" data-bs-toggle="tab" data-bs-target="#pharmacy-returns" type="button" role="tab" aria-controls="pharmacy-returns" aria-selected="false">Pharmacy Returns
                <span id="lab-pharmacy-badge" class="badge bg-danger badge-number"></span></button>
              </li>
            <!-- <li class="nav-item" role="presentation">
              <button class="nav-link" id="inpatients-tab" data-bs-toggle="tab" data-bs-target="#inpatients" type="button" role="tab" aria-controls="inpatients" aria-selected="false">In-Patients
                <span id="inpatients-badge" class="badge bg-danger badge-number"></span></button>
              </li> -->
            </ul>
            
            <div class="tab-content pt-2" id="myTabContent">
              <div class="tab-pane fade show active" id="reception" role="tabpanel" aria-labelledby="reception-tab">
                <div class="table-responsive">
                  <table id="table_reception_patients" class="table table-striped table-sm nowrap" style="width:100%">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Full Name</th>
                        <th scope="col">File Number</th>
                        <th scope="col">Receptionist</th>
                        <th scope="col">Entry Time</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="tab-pane fade" id="lab" role="tabpanel" aria-labelledby="lab-tab">
                <table id="table_lab_patients" class="table table-striped table-sm nowrap" style="width:100%">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Full Name</th>
                      <th scope="col">File Number</th>
                      <th scope="col">Receptionist</th>
                      <th scope="col">Referrer</th>
                      <th scope="col">Entry Time</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
              <div class="tab-pane fade" id="lab-returns" role="tabpanel" aria-labelledby="lab-returns-tab">
                <table id="table_lab_returns" class="table table-striped table-sm nowrap" style="width:100%">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Full Name</th>
                      <th scope="col">File Number</th>
                      <th scope="col">Receptionist</th>
                      <th scope="col">Referrer</th>
                      <th scope="col">Entry Time</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
              <div class="tab-pane fade" id="pharmacy-returns" role="tabpanel" aria-labelledby="pharmacy-returns-tab">
                <table id="table_pharmacy_returns" class="table table-striped table-sm nowrap" style="width:100%">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Full Name</th>
                      <th scope="col">File Number</th>
                      <th scope="col">Receptionist</th>
                      <th scope="col">Referrer</th>
                      <th scope="col">Entry Time</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
           <!--  <div class="tab-pane fade show" id="inpatients" role="tabpanel" aria-labelledby="inpatients-tab">
                <table id="table_inpatients" class="table table-striped table-sm nowrap" style="width:100%">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Full Name</th>
                      <th scope="col">File Number</th>
                      <th scope="col">Receptionist</th>
                      <th scope="col">Entry Time</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div> -->
            </div>

          </div>
        </div>
      </div>

    </div>
  </section>
  <?php $this->load->view('templates/base_footer.php'); ?>

  <script type="text/javascript">
    $(function() {

  // Retrieve patient data on page load: from reception
      const title = 'Doctor section | List of Patients From Reception'
      var table_reception_patients = $('#table_reception_patients').DataTable({
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
          url : "<?php echo base_url('doctor/patients-from-reception');?>",
          type : 'POST'
        },
        "ordering": false,
        "dom": 'Blfrtip',
        "buttons": [
        {
          extend: "copy",
          title: title,
        },
        {
          extend: "excel",
          title: title,
        },
        {
          extend: "csv",
          title: title,
        },
        {
          extend: "pdf",
          title: title,
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5],
          },
        },
        {
          extend: "print",
          title: title,
        },
        ],
      });
      
      
  // Retrieve patient data on page load: from lab
      const title2 = 'Doctor section | List of Patients From Lab'
      var table_lab_patients = $('#table_lab_patients').DataTable({
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
          url : "<?php echo base_url('doctor/patients-from-lab');?>",
          type : 'POST'
        },
        "ordering": false,
        "dom": 'Blfrtip',
        "buttons": [
        {
          extend: "copy",
          title: title2,
        },
        {
          extend: "excel",
          title: title2,
        },
        {
          extend: "csv",
          title: title2,
        },
        {
          extend: "pdf",
          title: title2,
        },
        {
          extend: "print",
          title: title2,
        },
        ],
      });
      
      
  // Retrieve returned patients from lab
      const return_lab_title = 'Doctor section | List of Patients Returned From Lab'
      var table_lab_returns = $('#table_lab_returns').DataTable({
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
          url : "<?php echo base_url('doctor/patients-lab-returns');?>",
          type : 'POST'
        },
        "ordering": false,
        "dom": 'Blfrtip',
        "buttons": [
        {
          extend: "copy",
          title: return_lab_title,
        },
        {
          extend: "excel",
          title: return_lab_title,
        },
        {
          extend: "csv",
          title: return_lab_title,
        },
        {
          extend: "pdf",
          title: return_lab_title,
        },
        {
          extend: "print",
          title: return_lab_title,
        },
        ],
      });

  // Retrieve returned patients from lab
      const return_pharmacy_title = 'Doctor section | List of Patients Returned From Pharmacy'
      var table_pharmacy_returns = $('#table_pharmacy_returns').DataTable({
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
          url : "<?php echo base_url('doctor/patients-pharmacy-returns');?>",
          type : 'POST'
        },
        "ordering": false,
        "dom": 'Blfrtip',
        "buttons": [
        {
          extend: "copy",
          title: return_pharmacy_title,
        },
        {
          extend: "excel",
          title: return_pharmacy_title,
        },
        {
          extend: "csv",
          title: return_pharmacy_title,
        },
        {
          extend: "pdf",
          title: return_pharmacy_title,
        },
        {
          extend: "print",
          title: return_pharmacy_title,
        },
        ],
      });

  // // Retrieve in-patients list, on page load
  // const in_title = 'Doctor section | List of In-Patients'
  // var table_inpatients = $('#table_inpatients').DataTable({
  //   oLanguage: {
  //     sProcessing: "",      
  //     sLengthMenu: 'Show <select class="form-select">'+
  //     '<option value="10">10</option>'+
  //     '<option value="50">50</option>'+
  //     '<option value="100">100</option>'+
  //     '<option value="500">500</option>'+
  //     '<option value="-1">All</option>'+
  //     '</select> records'
  //   },
  //   responsive: true,
  //   "processing":true,
  //   "serverSide":true,
  //   "order":[],
  //   "ajax": {
  //     url : "<f?php echo base_url('doctor/get-inpatients');?>",
  //     type : 'POST'
  //   },
  //   "ordering": false,
  //   "dom": 'Blfrtip',
  //   "buttons": [
  //   {
  //     extend: "copy",
  //     title: in_title,
  //   },
  //   {
  //     extend: "excel",
  //     title: in_title,
  //   },
  //   {
  //     extend: "csv",
  //     title: in_title,
  //   },
  //   {
  //     extend: "pdf",
  //     title: in_title,
  //   },
  //   {
  //     extend: "print",
  //     title: in_title,
  //   },
  //   ],
  // });

  // Reload data on div Click
      $("#reception-tab").click( function() {
        table_reception_patients.ajax.reload();
      });
      $("#lab-tab").click( function() {
        table_lab_patients.ajax.reload();
      });
      $("#lab-returns-tab").click( function() {
        table_lab_returns.ajax.reload();
      });
      $("#pharmacy-returns-tab").click( function() {
        table_pharmacy_returns.ajax.reload();
      });
  // $("#inpatients-tab").click( function() {
  //   table_inpatients.ajax.reload();
  // });


  // Respond on "Serve" click: patients from reception
      $("#table_reception_patients tbody").on('click', 'button', function() {
       if(this.name == "initial_serve") {
         var id = $(this).attr('data-id');
         var name = $(this).attr('data-patient');
         var file_no = $(this).attr('data-file');
         var visit = $(this).attr('data-visit');
         bootbox.confirm({
          message:
          'Do you really want to select <code>' + name + '</code> with personal file number <code>"' + file_no + '"</code>?',
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
                message:
                '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                closeButton: false,
              })
              .on("shown.bs.modal", function () {
                $.ajax({
                  url: "<?php echo base_url('doctor/serve-initial'); ?>",
                  type: "POST",
                  dataType: "json",
                  data: { record: id, visit: visit },
                  success: function (response) {
                    table_reception_patients.ajax.reload();
                    if (response.status) {
                      bootbox.alert(response.data.toString(), function () {
                        dialog.modal("hide");
                        window.location.href = response.redirect;
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
          },
        });
       }
     });


// Respond on "Serve" click: patients done lab
      $("#table_lab_patients tbody").on('click', 'button', function() {
       if(this.name == "serve_fromLab") {
         var id = $(this).attr('data-id');
         var name = $(this).attr('data-patient');
         var file_no = $(this).attr('data-file');
         var visit = $(this).attr('data-visit');
         bootbox.confirm({
          message:
          'You have selected <code>' + name + '</code> with personal file number <code>"' + file_no + '"</code>',
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
                message:
                '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                closeButton: false,
              })
              .on("shown.bs.modal", function () {
                $.ajax({
                  url: "<?php echo base_url('doctor/serve-from-lab'); ?>",
                  type: "POST",
                  dataType: "json",
                  data: { record: id, visit: visit },
                  success: function (response) {
                    table_lab_patients.ajax.reload();
                    if (response.status) {
                      bootbox.alert(response.data.toString(), function () {
                        dialog.modal("hide");
                        window.location.href = response.redirect;
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
          },
        });
       }
     });

// Respond on "Serve" click: patients lab returns
      $("#table_lab_returns tbody").on('click', 'button', function() {
       if(this.name == "serve_LabReturn") {
         var id = $(this).attr('data-id');
         var name = $(this).attr('data-patient');
         var file_no = $(this).attr('data-file');
         var visit = $(this).attr('data-visit');
         bootbox.confirm({
          message:
          'You have selected a returned patient with name <code>' + name + '</code> and PF number <code>"' + file_no + '"</code>',
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
                message:
                '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                closeButton: false,
              })
              .on("shown.bs.modal", function () {
                $.ajax({
                  url: "<?php echo base_url('doctor/serve-lab-return'); ?>",
                  type: "POST",
                  dataType: "json",
                  data: { record: id, visit: visit },
                  success: function (response) {
                    table_lab_returns.ajax.reload();
                    if (response.status) {
                      bootbox.alert(response.data.toString(), function () {
                        dialog.modal("hide");
                        window.location.href = response.redirect;
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
          },
        });
       }
     });

// Respond on "Update" click: patients pharmacy returns
      $("#table_pharmacy_returns tbody").on('click', 'button', function() {
       if(this.name == "serve_PhReturn") {
         var id = $(this).attr('data-id');
         var name = $(this).attr('data-patient');
         var file_no = $(this).attr('data-file');
         var visit = $(this).attr('data-visit');
         bootbox.confirm({
          message:
          'You have selected a returned patient with name <code>' + name + '</code> and PF number <code>"' + file_no + '"</code>',
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
                message:
                '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
                closeButton: false,
              })
              .on("shown.bs.modal", function () {
                $.ajax({
                  url: "<?php echo base_url('doctor/serve-pharmacy-return'); ?>",
                  type: "POST",
                  dataType: "json",
                  data: { record: id, visit: visit },
                  success: function (response) {
                    table_pharmacy_returns.ajax.reload();
                    if (response.status) {
                      bootbox.alert(response.data.toString(), function () {
                        dialog.modal("hide");
                        window.location.href = response.redirect;
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
          },
        });
       }
     });


      function count_patients_from_reception(){
        $.ajax({
          url: "<?php echo base_url('doctor/count-patients-from-reception'); ?>",
          type: "POST",
          dataType: "json",
          success: function (response) {
            if(response.status) {
              const res = response.data;
              $("#done-reception-badge").text(res);       
            }
          }
        });
      }

      function count_patients_from_lab(){
        $.ajax({
          url: "<?php echo base_url('doctor/count-patients-from-lab'); ?>",
          type: "POST",
          dataType: "json",
          success: function (response) {
            if(response.status) {
              const res = response.data;
              $("#done-lab-badge").text(res);       
            }
          }
        });
      }

      function count_patients_lab_returns(){
        $.ajax({
          url: "<?php echo base_url('doctor/count-patients-lab-returns'); ?>",
          type: "POST",
          dataType: "json",
          success: function (response) {
            if(response.status) {
              const res = response.data;
              $("#lab-returns-badge").text(res);       
            }
          }
        });
      }

      function count_patients_pharmacy_returns(){
        $.ajax({
          url: "<?php echo base_url('doctor/count-patients-pharmacy-returns'); ?>",
          type: "POST",
          dataType: "json",
          success: function (response) {
            if(response.status) {
              const res = response.data;
              $("#lab-pharmacy-badge").text(res);       
            }
          }
        });
      }

      function count_inpatients(){
        $.ajax({
          url: "<?php echo base_url('doctor/count-inpatients'); ?>",
          type: "POST",
          dataType: "json",
          success: function (response) {
            if(response.status) {
              const res = response.data;
              $("#inpatients-badge").text(res);       
            }
          }
        });
      }

      count_patients_from_reception();
      setInterval(function(){
        count_patients_from_reception();
      }, 6000);

      count_patients_from_lab();
      setInterval(function(){
        count_patients_from_lab();
      }, 8000);

      count_patients_lab_returns();
      setInterval(function(){
        count_patients_lab_returns();
      }, 10000);

      count_patients_pharmacy_returns();
      setInterval(function(){
        count_patients_pharmacy_returns();
      }, 12000);

// count_inpatients();
// setInterval(function(){
//     count_inpatients();
//   }, 20000);


    });
  </script>