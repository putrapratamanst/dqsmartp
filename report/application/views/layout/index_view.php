<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $title; ?></title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous" />
		<link href="<?php echo base_url("assets/css/jquery.ui.datepicker.min.css"); ?>" rel="stylesheet" />
		<link href="<?php echo base_url("assets/css/styles.css"); ?>" rel="stylesheet" />
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

		<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.min.js"); ?>"></script>
		<script type="text/javascript" src="<?php echo base_url("assets/js/jquery-ui.min.js"); ?>"></script>
	</head>
	<body>
		<?php
            echo $menu;
            echo $content;
        ?>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

		<script>
			$(".datepicker").datepicker({
				showOtherMonths: true,
				selectOtherMonths: true,
				changeMonth: true,
				changeYear: true,
				dateFormat: 'dd-mm-yy',
				maxDate: 0,
			});

			$(".select2").select2({
				theme: 'bootstrap-5'
			});
			// const options = {
			// 	enableHighAccuracy: true,
			// 	timeout: 10000,
			// };

			// const successCallback = (position) => {
			// 	//console.log(position);
			// };
			
			// const errorCallback = (error) => {
			// 	//console.log("err: " + error);
			// };
			
			// navigator.geolocation.getCurrentPosition(
			// 	successCallback, 
			// 	errorCallback,
			// 	options
			// );
		</script>
	</body>
</html>