<?php $this->load->view('templates/base_header.php'); ?>
<section class="section">
  <div class="row">

    <div class="col-lg-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Database Backup<span>| Recent</span></h5>
          </div>
          <table id="table_backup" class="table nowrap">
            <thead>
              <tr>
                <th scope="col">Backup File</th>
                <th scope="col">File Size</th>
                <th scope="col">Backup Date</th>
                <th scope="col">Author</th>
                <th scope="col">Option</th>
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

    loadBackup();
    function loadBackup(){
      $.ajax({
        url: "<?php echo base_url('reports/db-backup/'.@$header); ?>",
        type: "POST",
        dataType: "json",
        cache: false,
        success: function(data){
          // console.log(data);
          $('#table_backup tbody').html(data); 
        },
        error: function (jqXHR, textStatus, errorThrown) {
         // console.log(jqXHR);
        },
      });
    }

    
    
    $("#table_backup tbody").on('click', 'a', function(e) {
      if(this.name == "backup") {
        e.preventDefault();
        bootbox.confirm({message: 'You are about to start a Database Backup',
          buttons: {confirm: {label: '<i class="fa fa-check"></i> Agree',className: "btn-success",},
          cancel: {label: '<i class="fa fa-times"></i> Disagree',className: "btn-danger",},},
          callback: function (result) {
            if (result == true) {
              var dialog = bootbox.dialog({
                message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Resetting...</div>',
                closeButton: false,
              }).on("shown.bs.modal", function () {
                $.ajax({
                  url: "<?php echo base_url('reports/start-db-backup'); ?>",
                  type: "POST",
                  dataType: "json",
                  data: {},
                  success: function (response) {
                    // console.log(response);
                    if (response.status) {
                      bootbox.alert(response.data.toString(), function () {
                        dialog.modal("hide");
                        loadBackup();
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
                      // console.log(jqXHR);
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