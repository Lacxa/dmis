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
							Stock management section <span>| Choose action</span>
						</h5>
						<div class="col-12">
							<select id="action_input" class="form-select">
								<option value="" selected>Choose...</option>
								<option value="1">CREATE NEW BATCH</option>
								<option value="2">ADD MEDICINES</option>
								<option value="3">POST BATCH</option>
								<option value="4">REMOVE BATCH</option>
							</select>
						</div>
						<span class="mt-2" id="form-area">
							<form class="row g-3 mt-2" id="new-stock-form" action="javascript:void(0);" style="display: none;">
								<input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
								<div class="col-lg-6 col-md-6">
									<label for="supplier" class="form-label"> Supplier <span class="text-danger">*</span> </label>
									<input type="text" class="form-control" name="supplier" id="supplier">
								</div>
								<div class="col-lg-6 col-md-6">
									<div class="container"><label for="entry_date" class="form-label"> Date of Entry <span class="text-danger"> * </span></label><div class="datepicker input-group date"><input type="text" class="form-control" placeholder="Choose entry date" name="entry_date" id="entry_date" autoComplete="off"/><span class="input-group-append"><span class="input-group-text bg-light d-block"><i class="bi bi-calendar3"></i></span></span></div></div>
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
									<label for="stock" class="form-label"> Batch <span class="text-danger">*</span> </label>
									<select name="stock" id="stock" class="form-select">
									</select>
								</div>

								<div class="col-md-4">
									<label for="category" class="form-label"> Category <span class="text-danger">*</span> </label>
									<select name="category" id="category" class="form-select">
									</select>
								</div>

								<div class="col-md-4">
									<label for="form" class="form-label"> Form </label>
									<select name="form" id="form" class="form-select">
									</select>
								</div>

								<div class="col-md-4">
									<label for="medicine" class="form-label"> Medicine <span class="text-danger">*</span> </label>
									<select name="medicine" id="medicine" class="form-select">
									</select>
								</div>

								<div class="col-md-4">
									<label for="unit" class="form-label"> Unit <span class="text-danger">*</span> </label>
									<select name="unit" id="unit" class="form-select">
									</select>
								</div>

								<div class="col-md-4">
									<label for="unit_value" class="form-label"> Unit value<span class="text-danger">*</span> </label>
									<input class="form-control" name="unit_value" id="unit_value" />
								</div>

								<div class="col-md-4">
									<label for="total" class="form-label"> Count <span class="text-danger">*</span></label>
									<input class="form-control numberonly" name="total" id="total" autocomplete="off" />
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
									<div class="d-grid gap-2">
										<button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Post Batch</button>
									</div>
								</div>
							</form>

							<form class="row g-3 mt-2" id="remove-stock-form" action="javascript:void(0);" style="display: none;">
								<input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>" />
								<div class="col-md-9">
									<input type="text" placeholder="Enter Token" class="form-control" name="token" id="token" autocomplete="off">
								</div>
								<div class="col-md-3">
									<div class="d-grid gap-2">
										<button type="submit" class="btn btn-primary"><i class="bi bi-trash3 me-1"></i> Remove Batch</button>
									</div>
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
						<h5 class="card-title">A list of medical stock</h5>
						<button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#manageStockCollapse">
							<i class="bi bi-plus-circle me-1"></i> Manage Stock
						</button>
					</div>
					<span id="stock_span"></span>
					<span id="stock_pagination"></span>
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

		get_stocks(0);
		$('span#stock_pagination').on('click','a',function(e){
			e.preventDefault();
			var pageNum = $(this).attr('data-ci-pagination-page');
			get_stocks(pageNum);
		});

		function get_stocks(pageNum) {
			$.ajax({
				type: "GET",  
				url: `<?php echo base_url('pharmacy/get-stock-register/')?>${pageNum}`,  
				data: "{}",
				dataType: 'json',
				success: function (resp) {
					$("span#stock_pagination").html(resp.pagination);
					setStocks(resp.stockData);
				}
			});			
		}

		function setStocks(data){
			// console.log(data);
			var html = '';
			if(data.length == 0){
				html += '<div class="alert alert-warning alert-dismissible fade show" role="alert"><i class="bi bi-info-circle me-1"></i>Oops!, stock is empty!</div>';
			} else {
				
				var headerArray = [];
				$('#stockAccordion .collapse').each(function(){
					const elementID = this.id;
					if($(`#${elementID}`).hasClass("show")){
						headerArray.push(elementID);
					}
				});
								
				html += '<div class="accordion" id="stockAccordion">';
				$.each(data, function(key, value){
					const active = value.state == 0 ? '<span class="badge bg-danger">Draft</span>': '';
					html += '<div class="accordion-item">';
					html += `<h2 class="accordion-header" id="heading_${value.id}">`;
					html += `<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_${value.id}" aria-expanded="true" aria-controls="collapseOne"><i class="bi bi-collection"></i>&nbsp;BATCH NUMBER&nbsp;<code>${value.token}</code> <span class="badge bg-light text-dark">#${value.supplier}</span>&nbsp;${active}&nbsp;(${value.entry})</button>`;
					html += '</h2>';
					html += `<div id="collapse_${value.id}" class="accordion-collapse collapse" aria-labelledby="heading_${value.id}" data-bs-parent="#stockAccordion">`;
					html += '<div class="accordion-body">';
					const sub = value.sub;
					if(sub.length == 0){
						html += '<div class="alert alert-warning alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-octagon me-1"></i>Empty batch!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
					}else{
						html += '<div class="table-responsive">';
						html += '<table class="table table-bordered table-sm" id="nestedTable">';
						html += '<tbody>';
						$.each(sub, function(key1, val1){
							html += '<tr>';
							html += `<td class="align-middle">${key1.toUpperCase()}</td>`;
							html += '<td>';
							html += '<table class="table table-striped table-sm mb-0">';
							html += '<thead>';
							html += `<tr>
								<th scope="col">#</th>
				                                <th scope="col">Name</th>
				                                <th scope="col">Other Name</th>
				                                <th scope="col">Form</th>
				                                <th scope="col">Unit</th>
				                                <th scope="col">Total</th>
				                                <th scope="col">Sold</th>
				                                <th scope="col">Available</th>${value.state == 0 ? '<th scope="col">Option</th>':''}
					                        </tr>`;
							html += '</thead>';
							html += '<tbody>';
							$.each(val1, function(key2, val2){html += `<tr>
									<th scope="row">${key2+1}</th>
					                                <td>${val2.medicine1}</td>
					                                <td>${val2.medicine2}</td>
					                                <td>${val2.form}</td>
					                                <td>${val2.unit_title}:&nbsp;${val2.unit_value}&nbsp;${val2.unit_name}</td>
					                                <td>${val2.total}</td>
					                                <td>${val2.usage == 0 ? val2.usage : '<a href="<?php echo base_url('pharmacy/stock-get-sold/') ?>'+val2.stock_token+'" class="text-primary">'+val2.usage+'</a>'}</td>
					                                <td>${val2.total-val2.usage}</td>${value.state == 0 ? `<td><a href="#" name="deleteMed" data-id="${val2.stock_id}" class="text-primary">Remove</a></td>`:''}
					                        </tr>`;
							});
							html += '</tbody>';
							html += '</table>';
							html += '</td>';
							html += '</tr>';
						});
						html += '</tbody>';
						html += '</table>';
						html += '</div>';
					}
					html += '</div>';
					html += '</div>';
					html += '</div>';
				});
				html += '</div>';
			}
			$("span#stock_span").html(html);
			
			$.each(headerArray, function(index, item){
				$(`#${item}`).addClass('show');
				// $(`#${item}`).collapse('show');
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
							url: '<?php echo base_url('pharmacy/stock-register/get-draft-batches') ?>',  
							data: "{}",
							dataType: 'json',
							timeout: 10000,
							success: function (response) {
								if(response.status){
									var htmlContent = '<option value="" selected> Choose stock...</option>';
									$.each(response.data, function(key, value) {
										htmlContent += `<option value="${value.id}">BATCH NUMBER "${value.code}" SUPPLIED BY "${value.supplier}" ON "${value.entry}"</option>`;
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
			var dialog = bootbox
			.dialog({
				message:
				'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',
				closeButton: false,
			})
			.on("shown.bs.modal", function () {
				$.ajax({
					type: "GET",  
					url: '<?php echo base_url('pharmacy/stock-register/get-stock-settings');?>', 
					data: "{}",
					dataType: 'json',
					timeout: 10000,
					success: function (response) {
						if(response.status){
							var data = response.data;
							let cats = data.categories;
							let forms = data.forms;
							let units = data.units;

							var catHtml = '<option value="" selected> Choose category...</option>';
							$.each(cats, function(key, value) {
								catHtml += `<option id="${value.token}" value="${value.token}">${value.title.toUpperCase()}</option>`;
							});
							$('form#add-medicine-form select#category').html(catHtml);

							var formsHtml = '<option value="" selected> Choose form...</option>';
							$.each(forms, function(key, value) {
								formsHtml += `<option id="${value.token}" value="${value.token}">${value.title.toUpperCase()}</option>`;
							});
							$('form#add-medicine-form select#form').html(formsHtml);

							var unitsHtml = '<option value="" selected> Choose a unit...</option>';
							$.each(units, function(key, value) {
								unitsHtml += `<option id="${value.token}" value="${value.token}">${value.unit} - ${value.title.toUpperCase()}</option>`;
							});
							$('form#add-medicine-form select#unit').html(unitsHtml);
							dialog.modal("hide");				
						}else{
							bootbox.alert(response.data.toString(), function () {
								$('form#add-medicine-form select#category').html('<option value="" selected> Choose category...</option>');
								$('form#add-medicine-form select#form').html('<option value="" selected> Choose form...</option>');
								$('form#add-medicine-unit select#form').html('<option value="" selected> Choose a unit...</option>');
								dialog.modal("hide");
							});
						}
					}
				});
			});
		});

		function setMedicineSelection(response){
			let medicines = response.data;
			let html = '<option value="" selected> Choose medicine...</option>';
			if(response.status){
				$.each(medicines, function(key, value) {
					html += `<option value="${value.token}">${value.name.toUpperCase()} (${value.name2.toUpperCase()})</option>`;
				});
			}$('form#add-medicine-form select#medicine').html(html);
		}

		$('select#category').on('change', function() {
			const category = this.value;
			if(category){
				var dialog = bootbox.dialog({message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',closeButton:false,}).on("shown.bs.modal", function () {
					$.ajax({
						type: "GET",  
						url: '<?php echo base_url('pharmacy/category-medicines/');?>'+category,
						data: "{}",
						dataType: 'json',
						timeout: 10000,
						success: function (response) {
							$('select#form').val("");
							setMedicineSelection(response);
							if(response.status == false){
								bootbox.alert(response.data.toString());
							}
							dialog.modal("hide")
						}
					});
				});
			}
		});

		$('select#form').on('change', function() {
			const form = this.value;
			const cat = $('select#category').val();
			if(form && cat){
				var dialog = bootbox.dialog({message:'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',closeButton:false,}).on("shown.bs.modal", function () {
					$.ajax({
						type: "GET",  
						url: `<?php echo base_url('pharmacy/category-form-medicines/');?>${cat}/${form}`,
						data: "{}",
						dataType: 'json',
						timeout: 10000,
						success: function (response) {
							setMedicineSelection(response);
							if(response.status == false){
								bootbox.alert(response.data.toString());
							}
							dialog.modal("hide")
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
						url: '<?php echo base_url('pharmacy/stock-register/create-new-batch');?>',
						type: "POST",
						data: formdata,
						dataType: "JSON",
						success: function (response) {
							if (response.status) {
								bootbox.alert(response.data.toString(), function () {
									get_stocks(0);
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
								// console.log(jqXHR)
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
				category: { required: true, number: true },
				form: { number: true },
				medicine: { required: true, number: true },
				unit: { required: true, number: true },
				unit_value: { required: true, maxlength: 100 },
				total: { required: true, number: true, min: 1, max: 500000 }
			},
			messages: {
				'stock': {required: "You must select a batch"},
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
						timeout: 10000,
						success: function (response) {
							if (response.status) {
								bootbox.alert(response.data.toString(), function () {
									get_stocks(0);
									$('form#add-medicine-form select#unit').val("");
									$('form#add-medicine-form input#unit_value').val("");
									$('form#add-medicine-form input#total').val("");
									dialog.modal("hide");
								});
							} else {
								bootbox.alert(response.data.toString(), function () {
									dialog.modal("hide");
								});
							}
						},
						error: function (jqXHR, textStatus, errorThrown) {
							// console.log(jqXHR);
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
							// console.log(response);
							if (response.status) {
								bootbox.alert(response.data.toString(), function () {
									get_stocks(0);
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
								// console.log(jqXHR);
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
							// console.log(response);
							if (response.status) {
								bootbox.alert(response.data.toString(), function () {
									get_stocks(0);
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

		$("span#stock_span").on('click', 'a', function() {
			var id = $(this).attr('data-id');
			if(this.name == "deleteMed") {
				var dialog = bootbox.dialog({message:
					'<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>',closeButton: false,}).on("shown.bs.modal", function () {
						$.ajax({
							url: `<?php echo base_url('pharmacy/stock-register/remove-stock-medicine/');?>${id}`,
							type: "POST",
							dataType: "JSON",
							timeout: 10000,
							success: function (response) {
								if(response.status){
									bootbox.alert(response.data.toString(), function () {
										get_stocks(0);
										dialog.modal("hide");
										});
								}else{
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
