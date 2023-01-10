<?php 
$this->load->view('pages/e_reports/header.php');


$employee_name = empty($basic['emp_mname']) ? @$basic['emp_fname'].' '.@$basic['emp_lname']: @$basic['emp_fname'].' '.@$basic['emp_mname'][0].'. '.@$basic['emp_lname'];

echo '<p style="text-align: center; text-transform: uppercase; font-weight: bolder;"><span style="font-size: 14px;">EMPLOYEE SERVICE REPORT<br />' . @$employee_name . ' (' .@$basic['emp_pf']. ') FROM ' . $start . ' TO ' . $end . '</span> <br /><span style="font-size: 12px;">(NIT DISPENSARY)</span></p>';
?>


<?php if(!empty(@$data)) { ?>
	<br />
	<table style="width: 100%;font-size: 11px; table-layout: auto; border: 1px solid black; border-collapse: collapse;">	
		<tr nobr="true"> 
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 7%;"> S/N </th>
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: <?php if(@$lab == TRUE) echo '26%;';else echo '29%;';?>">Patient Name</th>
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 16%;">Patient File</th>
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 8%;">Gender</th>
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 20%;">Occupation</th>
			<?php if(@$lab == TRUE) echo '<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 5%;">Lab</th>'; ?>
			<th style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: <?php if(@$lab == TRUE) echo '18%;';else echo '20%;';?>">Date of Service</th>
		</tr>
		<?php foreach ($data as $key => $row) { ?>
			<tr nobr="true">
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 7%;"><?php echo @$key+1;?></td>
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: <?php if(@$lab == TRUE) echo '26%;';else echo '29%;';?>"><?php echo @$row['pat_lname'].',&nbsp;'.@$row['pat_fname']; ?></td>
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 16%;"><?php echo @$row['pat_file_no'];?></td>
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 8%;"><?php echo @$row['pat_gender'][0];?></td>
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 20%;"> <?php echo @$row['pat_occupation'];?></td>
				<?php if(@$lab == TRUE) {
					$r = @$row['sy_lab'] == 1 ? "YES" : "NO";
					echo '<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 5%;">'.$r.' </td>';
				}?>
				<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: <?php if(@$lab == TRUE) echo '18%;';else echo '20%;';?>"><?php echo @$row['rec_regdate']; ?></td>
				</tr><?php } ?>		
				<tr nobr="true">
					<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;" colspan="<?php echo @$lab == TRUE ? '6' : '5';?>"> <span style="text-align: right; font-weight: bolder;"> Total: </span>
					</td>
					<td style="border: 1px solid black; padding: 5px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"> <?php echo count(@$data);?></td>
				</tr>
			</table>
		<?php } ?>
