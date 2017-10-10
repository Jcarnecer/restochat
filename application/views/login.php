<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Simple Apps</title>
		<base href="http://localhost/main/" />
		<!-- <link rel="stylesheet" type="text/css" href="assets/css/flavored-reset-and-normalize.min.css" >
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" > -->
		<link rel="stylesheet" type="text/css" href="assets/css/flavored-reset-and-normalize.css" >
		<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css" >
		<link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css" >
		<link rel="stylesheet" type="text/css" href="assets/css/styles.css" >
	</head>
	<body>
		
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-sm-6">
					<div class="card card-primary">
						<div class="card-header">
							Login
						</div>
						<div class="card-body">
							<?php if ($error != null): ?>
								<div class="alert alert-danger">
									<?= $error ?>
								</div>
							<?php endif; ?>
							<form id="loginUser" method="POST">
								<div class="form-group row">
									<label class="col-sm-4 col-form-label text-right">E-mail Address</label>
									<div class="col-sm-8">
										<input class="form-control" type="text" name="email_address" placeholder="E-mail Address" />
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-4 col-form-label text-right">Password</label>
									<div class="col-sm-8">
										<input class="form-control" type="password" name="password" placeholder="Password" />
									</div>
								</div>
							</form>
						</div>
						<div class="card-footer">
							<button class="btn btn-primary" type="submit" form="loginUser">Login</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script src="assets/js/jquery.js"></script>
		<script src="assets/js/popper.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/jquery.nicescroll.min.js"></script>
		<script src="assets/js/script.js"></script>
	</body>
</html>