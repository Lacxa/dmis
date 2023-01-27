<?php $this->load->view('templates/base_header.php'); ?>
<section class="section">
  <div class="row">

    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">A list of incomplete patients</h5>
          </div>
          <table id="table_incomplete" class="table table-striped table-sm nowrap" style="width:100%">
            <thead>
              <tr>
                <th scope="col">Name</th>
                <th scope="col">File</th>
                <th scope="col">Mobile</th>
                <th scope="col">Occupation</th>
                <th scope="col">Entry Date</th>
                <th scope="col">Failure Point</th>
                <th scope="col">Option</th>
                <th scope="col">Address</th>
                <th scope="col">Sex</th>
                <th scope="col">Emergency Contact</th>
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
    
    
    const title = 'A list of incomplete patients';
    var table_incomplete = $('#table_incomplete').DataTable({
      oLanguage: {
        sProcessing: "loading...",
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
        url : "<?php echo base_url('reports/incomplete-patients/'.@$header); ?>",
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
    
    
    $("#table_incomplete tbody").on('click', 'a', function() {
      if(this.name == "resetIncomplete") {
        var id = $(this).attr('data-id');
        bootbox.confirm({message: 'You are about to reset',
          buttons: {confirm: {label: '<i class="fa fa-check"></i> Yes',className: "btn-success",},
          cancel: {label: '<i class="fa fa-times"></i> No',className: "btn-danger",},},
          callback: function (result) {
            if (result == true) {
              var dialog = bootbox.dialog({
                message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Resetting...</div>',
                closeButton: false,
              }).on("shown.bs.modal", function () {
                $.ajax({
                  url: "<?php echo base_url('reports/incomplete-patients/reset/'); ?>"+id,
                  type: "POST",
                  dataType: "json",
                  data: {},
                  success: function (response) {
                    if (response.status) {
                      dialog.modal("hide");
                      // console.log(response);
                      table_incomplete.ajax.reload();
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
                  timeout: 10000
                });               
              });
        }
      }
    });
    }
  });
    
    
  });
</script>