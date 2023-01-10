<?php $this->load->view('templates/base_header.php');
$csrf = array(
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
);
?>

<section class="section">
	<div class="row">
		
		<div class="col-lg-12">
			<div class="collapse" id="manageStockCollapse">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">
							Select the relevant action
						</h5>
						<div class="row g-3">
							<div class="col-12">
								<select id="action_input" class="form-select">
									<option value="" selected>Choose...</option>
									<option value="1">Initiate new stock</option>
									<option value="2">Add medicines</option>
									<option value="3">Post stock</option>
									<option value="4">Remove stock/medicine</option>
								</select>
							</div>
						</div>
						<span class="mt-2" id="form-area">

							<form class="row g-3 mt-2" id="new-stock-form" action="javascript:void(0);" style="display: none;">
								<input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
								<div class="col-lg-6 col-md-6">
									<label for="supplier" class="form-label"> Supplier <span class="text-danger">*</span> </label>
									<input type="text" class="form-control" name="supplier" id="supplier">
								</div>
								<div class="col-lg-6 col-md-6">
									<div class="container">
										<label for="entry_date" class="form-label"> Date of Entry <span class="text-danger"> * </span></label>
										<div class="datepicker input-group date">
											<input type="text" class="form-control" placeholder="Choose entry date" name="entry_date" id="entry_date" autoComplete="off"/>
											<span class="input-group-append">
												<span class="input-group-text bg-light d-block">
													<i class="bi bi-calendar3"></i>
												</span>
											</span>
										</div>
									</div>
								</div>
								<div class="col-lg-12 col-md-12">
									<label for="description" class="form-label"> Description (if any) </label>
									<textarea class="form-control" name="description" id="description"></textarea>
								</div>
								<div class="text-center">
									<button type="submit" class="btn btn-primary"> Submit </button>
									<button type="reset" class="btn btn-secondary"> Reset </button>
								</div>
							</form>

							<form class="row g-3 mt-2 p-1 border rounded" id="add-medicine-form" action="javascript:void(0);" style="display: none;">
								<input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
								<div class="col-12">
									<label for="stock" class="form-label"> Select stock <span class="text-danger">*</span> </label>
									<select name="stock" id="stock" class="form-select">
									</select>
								</div>

								<div class="col-12">
									<label for="path" class="form-label"> Select path <span class="text-danger">*</span> </label>
									<select name="path" id="path" class="form-select">
									</select>
								</div>

								<div class="col-12">
									<label for="medicine" class="form-label"> Select medicine
										<span class="text-danger">*</span>
									</label>
									<select name="medicine" id="medicine" class="form-select">
									</select>
								</div>

								<div class="col-lg-6 col-md-6">
									<label for="description" class="form-label"> Description (Eg.Mass, weight, volume, etc) <span class="text-danger">*</span> </label>
									<input class="form-control" name="description" id="description" />
								</div>

								<div class="col-lg-6 col-md-6">
									<label for="total" class="form-label"> Count <span class="text-danger">*</span></label>
									<input class="form-control numberonly" name="total" id="total" />
								</div>

								<div class="text-center mb-2">
									<button type="submit" class="btn btn-primary"> Submit </button>
									<button type="reset" class="btn btn-secondary"> Reset </button>
								</div>
							</form>

							<form class="row g-3 mt-2" id="post-stock-form" action="javascript:void(0);" style="display: none;">
								<input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
								<div class="col-md-9">
									<input type="text" placeholder="Enter Token" class="form-control" name="token" id="token" autocomplete="off">
								</div>
								<div class="col-md-3">
									<button type="submit" class="btn btn-primary"> Post </button>
								</div>
							</form>

							<form class="row g-3 mt-2" id="remove-stock-form" action="javascript:void(0);" style="display: none;">
								<input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
								<div class="col-md-9">
									<input type="text" placeholder="Enter Token" class="form-control" name="token" id="token" autocomplete="off">
								</div>
								<div class="col-md-3">
									<button type="submit" class="btn btn-primary"> Remove </button>
								</div>
							</form>

						</span>				
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-12">
			<div class="card">
				<div class="card-body"> 
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="card-title">Recent medical stock</h5>
						<button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#manageStockCollapse">
							<i class="bi bi-plus-circle me-1"></i> Manage Stock
						</button>
					</div>
					<span id="stock_span"></span>
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
		
		$('.datepicker').datepicker({
			clearBtn: true,
			autoclose: true,
			changeMonth: true,
			changeYear: true,
			format: "yyyy-mm-dd",
		});

		get_stocks();

		function get_stocks() {
			$.ajax({
				type: "POST",  
				url: '<?php echo base_url('pharmacy/stock-register') ?>',  
				data: "{}",
				dataType: 'json',
				success: function (response) {
					// console.log(response);
					$("span#stock_span").html(response);
				}
			});			
		}

		$('select#action_input').on('change', function() {
			$('form#new-stock-form').hide();
			$('form#add-medicine-form').hide();
			$('form#post-stock-form').hide();
			$('form#remove-stock-form').hide();
			const value = this.value;
			if(value) {
				if(value == 1) {
					$("form#new-stock-form")[0].reset();
					$('form#new-stock-form').show();
				} else if(value == 2) {
					var dialog = bootbox
					.dialog({
						message:
						'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
						closeButton: false,
					})
					.on("shown.bs.modal", function () {
						$.ajax({
							type: "GET",  
							url: '<?php echo base_url('pharmacy/stock-register/get-draft') ?>',  
							data: "{}",
							dataType: 'json',
							success: function (response) {
								if(response.status){
									var htmlContent = '<option value="" selected> Choose stock...</option>';
									$.each(response.data, function(key, value) {
										htmlContent += `<option value="${value.id}">${value.code +'&nbsp;-&nbsp;'+ value.entry}</option>`;
									});						
									$('form#add-medicine-form select#stock').html(htmlContent);
									$("form#add-medicine-form")[0].reset();
									$('form#add-medicine-form').show();
									dialog.modal("hide");					
								} else {
									bootbox.alert(response.data.toString(), function () {
										dialog.modal("hide");
									});

								}
							}
						});
					});
				} else if(value == 3) {
					$("form#post-stock-form")[0].reset();
					$('form#post-stock-form').show();
				}  else if(value == 4) {
					$("form#remove-stock-form")[0].reset();
					$('form#remove-stock-form').show();
				}
			}
		});

		$('select#stock').on('change', function() {
			const id = this.value;
			if(id){
				var dialog = bootbox
				.dialog({
					message:
					'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
					closeButton: false,
				})
				.on("shown.bs.modal", function () {
					$.ajax({
						type: "GET",  
						url: '<?php echo base_url('pharmacy/stock-register/get-stock-paths/');?>'+id, 
						data: "{}",
						dataType: 'json',
						success: function (response) {
							if(response.status){
								var htmlContent = '<option value="" selected> Choose path...</option>';
								$.each(response.data, function(key, value) {
									htmlContent += `<option id="${value.cf}" value="${value.st_id}">${value.path}</option>`;
								});
								$('form#add-medicine-form select#path').html(htmlContent);
								dialog.modal("hide");				
							} else {
								bootbox.alert(response.data.toString(), function () {
									$('form#add-medicine-form select#path').html('<option value="" selected> Choose path...</option>');
									dialog.modal("hide");
								});

							}
						}
					});
				});
			}
		});

		$('select#path').on('change', function() {
			const id = this.value;
			const cat_and_format = $(this).children(":selected").attr("id");
			var arrayStr = cat_and_format.split(' > ');
			const category = arrayStr[0];
			const format = arrayStr[1];
			if(id){
				var dialog = bootbox
				.dialog({
					message:
					'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
					closeButton: false,
				})
				.on("shown.bs.modal", function () {
					$.ajax({
						type: "GET",  
						url: '<?php echo base_url('pharmacy/medicine-names/get-medicines-by-cat-format/');?>'+category+'/'+format,
						data: "{}",
						dataType: 'json',
						success: function (response) {
							if(response.status){
								var htmlContent = '<option value="" selected> Choose medicine...</option>';
								$.each(response.data, function(key, value) {
									htmlContent += `<option value="${value.token}">${value.text +' ('+value.short +') --> '+ value.category + ' | ' + value.format}</option>`;
								});				
								$('form#add-medicine-form select#medicine').html(htmlContent);
								dialog.modal("hide");				
							} else {
								bootbox.alert(response.data.toString(), function () {
									$('form#add-medicine-form select#medicine').html('<option value="" selected> Choose medicine...</option>');
									dialog.modal("hide");
								});

							}
						}
					});
				});
			}
		});


		$("form#new-stock-form").validate({
			errorPlacement: function(error, element) {
				error.addClass('text-danger');
				
				if (element.attr("name") == "entry_date") {
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
				supplier: { required: true, maxlength: 100},
				entry_date: { required: true },
				description: { maxlength: 300 },
			},
			highlight: function( element, errorClass, validClass ) {
				$(element).addClass(errorClass).removeClass(validClass);
			},
			unhighlight: function( element, errorClass, validClass ) {
				$(element).removeClass(errorClass).addClass(validClass);
			},
			submitHandler: function () {
				var dialog = bootbox
				.dialog({
					message:
					'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
					closeButton: false,
				})
				.on("shown.bs.modal", function () {
					var formdata = $("form#new-stock-form").serialize();
					$.ajax({
						url: '<?php echo base_url('pharmacy/stock-register/create-new-stock');?>',
						type: "POST",
						data: formdata,
						dataType: "JSON",
						success: function (response) {
							if (response.status) {
								bootbox.alert(response.data.toString(), function () {
									get_stocks();
									$("form#new-stock-form")[0].reset();
									// $('#manageStockCollapse').collapse('hide');
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

		$("form#add-medicine-form").validate({
			errorPlacement: function(error, element) {
				error.addClass('text-danger');
				error.insertAfter(element);
			},
			debug: false,
			errorClass: "is-invalid",
			validClass: "is-valid",
			errorElement: "div",
			rules: { 
				stock: { required: true },
				path: { required: true },
				medicine: { required: true },
				description: { required: true, maxlength: 100 },
				total: { required: true, number: true, min: 1, max: 500000 },
			},
			highlight: function( element, errorClass, validClass ) {
				$(element).addClass(errorClass).removeClass(validClass);
			},
			unhighlight: function( element, errorClass, validClass ) {
				$(element).removeClass(errorClass).addClass(validClass);
			},
			submitHandler: function () {
				var dialog = bootbox
				.dialog({
					message:
					'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
					closeButton: false,
				})
				.on("shown.bs.modal", function () {
					var formdata = $("form#add-medicine-form").serialize();
					$.ajax({
						url: '<?php echo base_url('pharmacy/stock-register/add-medicine-to-stock');?>',
						type: "POST",
						data: formdata,
						dataType: "JSON",
						success: function (response) {
							console.log(response);
							if (response.status) {
								bootbox.alert(response.data.toString(), function () {
									get_stocks();
									$("form#add-medicine-form")[0].reset();
									// $('#manageStockCollapse').collapse('hide');
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

		$("form#post-stock-form").validate({
			errorPlacement: function(error, element) {
				error.addClass('text-danger');
				error.insertAfter(element);
			},
			debug: false,
			errorClass: "is-invalid",
			validClass: "is-valid",
			errorElement: "div",
			rules: { 
				token: { required: true },
			},
			highlight: function( element, errorClass, validClass ) {
				$(element).addClass(errorClass).removeClass(validClass);
			},
			unhighlight: function( element, errorClass, validClass ) {
				$(element).removeClass(errorClass).addClass(validClass);
			},
			submitHandler: function () {
				var dialog = bootbox
				.dialog({
					message:
					'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
					closeButton: false,
				})
				.on("shown.bs.modal", function () {
					var formdata = $("form#post-stock-form").serialize();
					$.ajax({
						url: '<?php echo base_url('pharmacy/stock-register/post-stock');?>',
						type: "POST",
						data: formdata,
						dataType: "JSON",
						success: function (response) {
							console.log(response);
							if (response.status) {
								bootbox.alert(response.data.toString(), function () {
									get_stocks();
									$("form#post-stock-form")[0].reset();
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

		$("form#remove-stock-form").validate({
			errorPlacement: function(error, element) {
				error.addClass('text-danger');
				error.insertAfter(element);
			},
			debug: false,
			errorClass: "is-invalid",
			validClass: "is-valid",
			errorElement: "div",
			rules: { 
				token: { required: true },
			},
			highlight: function( element, errorClass, validClass ) {
				$(element).addClass(errorClass).removeClass(validClass);
			},
			unhighlight: function( element, errorClass, validClass ) {
				$(element).removeClass(errorClass).addClass(validClass);
			},
			submitHandler: function () {
				var dialog = bootbox
				.dialog({
					message:
					'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
					closeButton: false,
				})
				.on("shown.bs.modal", function () {
					var formdata = $("form#remove-stock-form").serialize();
					$.ajax({
						url: '<?php echo base_url('pharmacy/stock-register/remove-stock');?>',
						type: "POST",
						data: formdata,
						dataType: "JSON",
						success: function (response) {
							console.log(response);
							if (response.status) {
								bootbox.alert(response.data.toString(), function () {
									get_stocks();
									$("form#remove-stock-form")[0].reset();
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
