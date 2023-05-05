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
  <div class="row">

    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">

          <h5 class="card-title">
            Select the relevant report from the selection below
          </h5>
          <div class="row g-3">
            <div class="col-12">
              <select id="inputState" class="form-select">
                <option value="" selected>Choose...</option>
                <option value="1">PATIENTS: AGE/GENDER DISTRIBUTION REPORT</option>
                <option value="2">PATIENTS: DISEASE DISTRIBUTION REPORT</option>
                <option value="3">PATIENTS: LAB/NON-LAB REPORT</option>
                <option value="4">PATIENTS: DIAGNOSIS REPORT</option>
              </select>
            </div>
          </div>

          <span id="form-area">

            <div id="general_report" class="mt-4">

              <form class="row g-3" method="post" action="javascript:void(0);" id="general_form">
                <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
                <input type="hidden" name="report_type" id="report_type" value="0" />
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
                  <button type="reset" class="btn btn-secondary"> Reset </button>
                  <button type="submit" class="btn btn-primary"> Search </button>
                </div>
              </form>

              <div class="row mt-4 g-3 mx-2 table-responsive" id="age_gen_res" style="display:none;">
                <table id="age_gen_tb" class="table table-bordered table-sm caption-top cell-border" style="width:100%;">
                  <caption id="age_gen_caption"></caption>
                  <thead class="table-light">
                    <tr>
                      <th rowspan="2"></th>
                      <th colspan="3" class="text-center">0 - 1 YEAR</th>
                      <th colspan="3" class="text-center">1 - 5 YEARS</th>
                      <th colspan="3" class="text-center">5 - 60 YEARS</th>
                      <th colspan="3" class="text-center">ABOVE 60</th>
                    </tr>
                    <tr>
                      <th>Males</th>
                      <th>Females</th>
                      <th>Total</th>
                      <th>Males</th>
                      <th>Females</th>
                      <th>Total</th>
                      <th>Males</th>
                      <th>Females</th>
                      <th>Total</th>
                      <th>Males</th>
                      <th>Females</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                  <tfoot class="table-light">
                    <tr>
                      <th>Total</th><th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>

              <div class="row mt-4 g-3 mx-2 table-responsive" id="disease_res" style="display: none;">
                <table id="disease_tb" class="table table-bordered table-hover table-sm caption-top" style="width:100%;">
                  <caption id="disease_caption"></caption>
                  <thead class="table-light">
                    <tr>
                      <th rowspan="2" class="text-center">#</th>
                      <th rowspan="2" class="text-center">Disease</th>
                      <th rowspan="2" class="text-center">Code</th>
                      <th colspan="4" class="text-center">Patients</th>
                    </tr>
                    <tr>
                      <th>Students</th>
                      <th>Employees</th>
                      <th>Others</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                  <tfoot class="table-light">
                    <tr>
                      <th class="text-end" colspan="3">Total</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>

              <div class="row mt-4 g-3 mx-2 table-responsive" id="lab_res" style="display: none;">
                <table id="lab_tb" class="table table-bordered table-sm caption-top" style="width:100%;">
                  <caption id="lab_caption"></caption>
                  <thead class="table-light">
                    <tr>
                      <th rowspan="2" class="text-center">#</th>
                      <th colspan="4" class="text-center">Patients</th>
                    </tr>
                    <tr>
                      <th>Students</th>
                      <th>Employees</th>
                      <th>Others</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                  <tfoot class="table-light">
                    <tr>
                      <th class="text-end">Total</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>

              <div class="row mt-4 g-3 mx-2 table-responsive" id="diagnosis_res" style="display: none;">
                <table id="diagnosis_tb" class="table table-hover table-sm caption-top" style="width:100%;">
                  <caption id="diagnosis_caption"></caption>
                  <thead class="table-light">
                    <tr>
                      <th class="text-center">Category</th>
                      <th class="text-center">Diagnosis</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>

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
      todayHighlight: true,
      clearBtn: true,
      autoclose: true,
      changeMonth: true,
      changeYear: true,
      format: "yyyy-mm-dd",
    });

    $('.numberonly').keypress(function (e) {
      var charCode = (e.which) ? e.which : event.keyCode
      if (String.fromCharCode(charCode).match(/[^0-9]/g))
        return false;
    });

    var age_gen_dt = $('#age_gen_tb').DataTable({
      "dom": 'Brtip',
      "ordering": false,
      "buttons": [
        { extend: "copy", footer: true },
        { extend: "excel", footer: true },
        { extend: "csv", footer: true },
        { extend: "pdf", footer: true },
        { extend: "print", footer: true },
        ],
      "footerCallback": function (row, data, start, end, display) {
        var api = this.api(), data;

        // converting to interger to find total
        var intVal = function ( i ) {
          return typeof i === 'string' ?
          i.replace(/[\$,]/g, '')*1 :
          typeof i === 'number' ?
          i : 0;          
        }; 

        var group1 = api.column(3).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        var group2 = api.column(6).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        var group3 = api.column(9).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        var group4 = api.column(12).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        var total = group1 + group2 + group3 + group4

        $(api.column(1).footer()).html(total);
      },
    });


    var disease_dt = $('#disease_tb').DataTable({
      "dom": 'Blfrtip',
      "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
      "pageLength": 10,
      // "oLanguage": {
      //   sProcessing: "loading...",      
      //   sLengthMenu: 'Show <select class="form-select">'+
      //   '<option value="10">10</option>'+
      //   '<option value="50">50</option>'+
      //   '<option value="100">100</option>'+
      //   '<option value="500">500</option>'+
      //   '<option value="-1">All</option>'+
      //   '</select> records'
      // },
      "buttons": [
        { extend: "copy", footer: true },
        { extend: "excel", footer: true },
        { extend: "csv", footer: true },
        { extend: "pdf", footer: true },
        { extend: "print", footer: true },
        ],
        "fnDrawCallback": function(oSettings) {
          if ($('#disease_tb tr').length < 11) {
            $('#disease_tb .dataTables_paginate').hide();
          }
        },
      "footerCallback": function (row, data, start, end, display) {
        var api = this.api(), data;

        // converting to interger to find total
        var intVal = function ( i ) {
          return typeof i === 'string' ?
          i.replace(/[\$,]/g, '')*1 :
          typeof i === 'number' ?
          i : 0;
        };

        var studentTotal = api.column(3).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        var curr_studentTotal = api.column(3,{page:'current'}).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        
        var empTotal = api.column(4).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        var curr_empTotal = api.column(4,{page:'current'}).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        
        var otherTotal = api.column(5).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        var curr_otherTotal = api.column(5,{page:'current'}).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        
        var genTotal = api.column(6).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);        
        var curr_genTotal = api.column(6,{page:'current'}).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);

        $(api.column(3).footer()).html(curr_studentTotal+"/"+studentTotal);
        $(api.column(4).footer()).html(curr_empTotal+"/"+empTotal);
        $(api.column(5).footer()).html(curr_otherTotal+"/"+otherTotal);
        $(api.column(6).footer()).html(curr_genTotal+"/"+genTotal);
      },
    });

    var lab_dt = $('#lab_tb').DataTable({
      "dom": 'Brtip',
      "ordering": false,
      "buttons": [
        { extend: "copy", footer: true },
        { extend: "excel", footer: true },
        { extend: "csv", footer: true },
        { extend: "pdf", footer: true },
        { extend: "print", footer: true },
        ],
      "fnDrawCallback": function(oSettings) {
        if ($('#lab_tb tr').length < 11) {
          $('#lab_tb .dataTables_paginate').hide();
        }
      },
      "footerCallback": function (row, data, start, end, display) {
        var api = this.api(), data;

        // converting to interger to find total
        var intVal = function (i) {
          return typeof i === 'string' ?
          i.replace(/[\$,]/g, '')*1 :
          typeof i === 'number' ?
          i : 0;          
        }; 

        var group1 = api.column(1).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        var group2 = api.column(2).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        var group3 = api.column(3).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
        var group4 = api.column(4).data().reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);

        $(api.column(1).footer()).html(group1);
        $(api.column(2).footer()).html(group2);
        $(api.column(3).footer()).html(group3);
        $(api.column(4).footer()).html(group4);
      },
    });

    const columns = [
      { title: 'Diagnosis' },
      { title: 'Students' },
      { title: 'Employees' },
      { title: 'Others' },
      { title: 'Total' },
      ]

    var diagnosis_dt = $('#diagnosis_tb').DataTable({
      "dom": 'Blfrtip',
      "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
      "pageLength": 10,
      "buttons": [
        { extend: "copy", footer: true },
        { extend: "excel", footer: true },
        { extend: "csv", footer: true },
        { extend: "pdf", footer: true },
        { extend: "print", footer: true },
        ],
      createdRow: function(row) {
        $(row).find('td table').DataTable({
          columns: columns,
          dom: 'tp',
          "pageLength": 5,
        })
      },
    });

    $('select').on('change', function() {
      const value = this.value;
      if(value) {
        $('div#age_gen_res').hide();
        $('div#disease_res').hide();
        $('div#lab_res').hide();
        $('div#diagnosis_res').hide();
        $("form#general_form")[0].reset();
        $("form#general_form input#report_type").val(value);
      }
    });

    $("form#general_form").validate({
      errorPlacement: function(error, element) {
        error.addClass('text-danger');        
        if (element.attr("name") == "start" || element.attr("name") == "end") {
          error.insertAfter(element.parent('div'));
        } else {
          error.insertAfter(element);
        }
      },
      debug: false,
      errorClass: "is-invalid",
      validClass: "is-valid",
      errorElement: "div",
      rules: {
        start: { required: true },
        end: { required: true },
      },
      highlight: function( element, errorClass, validClass ) {
        $(element).addClass(errorClass).removeClass(validClass);
      },
      unhighlight: function( element, errorClass, validClass ) {
        $(element).removeClass(errorClass).addClass(validClass);
      },
      submitHandler: function (){
        if($("#inputState").val() != ""){
          var dialog = bootbox.dialog({message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Fetching results...</div>',closeButton: false,
        }).on("shown.bs.modal", function () {
          const form_val = parseInt($("form#general_form input#report_type").val());
          let url = '';
          if(form_val == 1) url = '<?php echo base_url('reports/age-gender');?>';
          else if(form_val == 2) url = '<?php echo base_url('reports/disease-distribution');?>';
          else if(form_val == 3) url = '<?php echo base_url('reports/lab-and-non-lab');?>';
          else if(form_val == 4) url = '<?php echo base_url('reports/diagnosis');?>';

          var formdata = $("form#general_form").serialize();
          $.ajax({
            url: url,
            type: "POST",
            data: formdata,
            dataType: "JSON",
            success: function (response) {
              if (response.status) {
                if(form_val == 1) setAgeGenderDistribution(response);
                else if(form_val == 2) setDiseaseDistribution(response);
                else if(form_val == 3) setLabAndNonLab(response);
                else if(form_val == 4) setDiagosisReport(response);
                dialog.modal("hide");
              } else {
                bootbox.alert(response.data.toString(), function () {
                  dialog.modal("hide");
                });
              }
            },
            error: function (jqXHR, textStatus, errorThrown) {
              bootbox.alert(errorThrown.toString(), function () {
                // console.log(jqXHR);
                dialog.modal("hide");
              });
            },
          });
        });
      }
    },
  });

    function setAgeGenderDistribution(response) {
      const group_1 = response.data.group_1[0];
      const group_2 = response.data.group_2[0];
      const group_3 = response.data.group_3[0];
      const group_4 = response.data.group_4[0];
      const total = parseInt(group_1.total) + parseInt(group_2.total) + parseInt(group_3.total) + parseInt(group_4.total);

      $("#age_gen_tb #age_gen_caption").html(`<em>Patients: age and gender distribution dated from <code>${$("form#general_form input#start").val()}</code> to <code> ${$("form#general_form input#end").val()} </code></em>`);

      age_gen_dt.clear().row.add(['', group_1.males, group_1.females, group_1.total, group_2.males, group_2.females, group_2.total, group_3.males, group_3.females, group_3.total, group_4.males, group_4.females, group_4.total]).draw(false);

      $('div#age_gen_res').show();
    }

    function setDiseaseDistribution(response) {      
      const data = response.data;
      disease_dt.clear().draw();
      $("#disease_tb #disease_caption").html(`<em>Patients: disease distribution dated from <code>${$("form#general_form input#start").val()}</code> to <code> ${$("form#general_form input#end").val()} </code></em>`);
      if(data.length > 0) {
        $.each(data, function(key, value){
          disease_dt.row.add([key+1, value.name, value.code, value.patients[0].students, value.patients[0].employees, value.patients[0].others, value.patients[0].total]).draw(false);
        });
      }
      $('div#disease_res').show();
    }

    function setLabAndNonLab(response){
      const data = response.data;
      lab_dt.clear().draw();
      $("#lab_tb #lab_caption").html(`<em>Patients: lab/non-lab distribution dated from <code>${$("form#general_form input#start").val()}</code> to <code> ${$("form#general_form input#end").val()} </code></em>`);
      const lab = data.lab[0];
      const nonLab = data.nonLab[0];
      lab_dt.rows.add([
        ['Lab', lab.students, lab.employees, lab.others, lab.total],
        ['Non-Lab', nonLab.students, nonLab.employees, nonLab.others, nonLab.total]
        ]).draw(false);
      $('div#lab_res').show();
    }

    function setDiagosisReport(response){
      const data = response.data;
      diagnosis_dt.clear().draw();    
      $("#diagnosis_tb #diagnosis_caption").html(`<em>Patients: diagnosis report dated from <code>${$("form#general_form input#start").val()}</code> to <code> ${$("form#general_form input#end").val()} </code></em>`);
      if(data.length > 0) {
        $.each(data, function(key, value){

          let subrows = '';
          $.each(value.sub, function(k, val){
            subrows += `<tr>
            <td>${val.name}</td>
            <td>${val.students}</td>
            <td>${val.employees}</td>
            <td>${val.others}</td>
            <td>${val.total}</td></tr>`;
          });

          diagnosis_dt.row.add([value.name, `<div id="${value.token}"><table id="${value.token}" class="table table-bordered table-sm subTable" style="width:100%;">
            <thead>
              <tr>
                <th rowspan="2" class="text-center">Diagnosis</th>
                <th colspan="4" class="text-center">Patients</th>
              </tr>
              <tr>
                <th>Students</th>
                <th>Employees</th>
                <th>Others</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>${subrows}</tbody>
            </table></div>`]).draw(false);
        });
      }
      $('div#diagnosis_res').show();
    }

  });
</script>