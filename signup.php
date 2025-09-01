<?php
require 'signupphp.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Registration Form</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="signup.css">
	<script>
		function handleRoleChange() {
			const role = document.getElementById("role").value;

			// Hide all conditional fields
			document.getElementById("institution-fields")?.classList.add("hidden");
			document.getElementById("institution-select-supervisor")?.classList.add("hidden");
			document.getElementById("institution-select-guard")?.classList.add("hidden");
			document.getElementById("location-section")?.classList.add("hidden");
			document.getElementById("idnumber-section")?.classList.add("hidden");

			// Show based on role
			if (role === "manager") {
				document.getElementById("institution-fields").classList.remove("hidden");
			}
			if (role === "supervisor") {
				document.getElementById("institution-select-supervisor").classList.remove("hidden");
				document.getElementById("location-section").classList.remove("hidden");
			}
			if (role === "guard") {
				document.getElementById("institution-select-guard").classList.remove("hidden");
				const guardInstitution = document.getElementById("institution_id_guard");
				if (guardInstitution && guardInstitution.value !== "") {
					document.getElementById("idnumber-section").classList.remove("hidden");
				}
			}
		}


	</script>
</head>

<body onload="handleRoleChange()" ;>
	<div class="container">
		<div class="title">Signup Here</div>
		<?php if (!empty($error['general'])): ?>
			<div class="error"><?= $error['general'] ?></div>
		<?php endif; ?>
		<?php if ($success): ?>
			<div class="success"><?= $success ?></div>
		<?php endif; ?>
		<form method="post" id="register" autocomplete="off">
			<div class="user-details">
				<div class="input-box">
					<span class="details">First Name</span>
					<input type="text" name="fname" maxlength="15" value="<?= htmlspecialchars($firstname) ?>" required>
					<?php if (!empty($error['firstname'])): ?>
						<div class="error"><?= $error['firstname'] ?></div><?php endif; ?>
				</div>
				<div class="input-box">
					<span class="details">Surname</span>
					<input type="text" name="surname" value="<?= htmlspecialchars($surname) ?>" required>
					<?php if (!empty($error['surname'])): ?>
						<div class="error"><?= $error['surname'] ?></div><?php endif; ?>
				</div>

				<div class="input-box">
					<span class="details">Email Address</span>
					<input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
					<?php if (!empty($error['email'])): ?>
						<div class="error"><?= $error['email'] ?></div><?php endif; ?>
				</div>
				<div class="input-box">
					<span class="details">Phone Number</span>
					<input type="text" name="phonenumber" maxlength="10" value="<?= htmlspecialchars($phonenumber) ?>"
						required>
					<?php if (!empty($error['phonenumber'])): ?>
						<div class="error"><?= $error['phonenumber'] ?></div><?php endif; ?>
				</div>
				<div class="input-box">
					<span class="details">Password</span>
					<input type="password" name="password" required>
					<?php if (!empty($error['password'])): ?>
						<div class="error"><?= $error['password'] ?></div><?php endif; ?>
				</div>
			</div>
			<div class="form-control">
				<label><span class="details">Select Your Role</span></label>
				<select name="role" id="role" onchange="handleRoleChange()" required>
					<option value="" disabled <?= $role == '' ? 'selected' : ''; ?>>Select your role</option>
					<option value="manager" <?= $role == 'manager' ? 'selected' : ''; ?>>Manager</option>
					<option value="supervisor" <?= $role == 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
					<option value="guard" <?= $role == 'guard' ? 'selected' : ''; ?>>Security Guard</option>
				</select>
			</div>
			<div id="institution-fields" class="<?= $role == 'manager' ? '' : 'hidden' ?>">
				<div class="input-box">
					<span class="details">Institution Name</span>
					<input type="text" name="institution_name" value="<?= htmlspecialchars($institution_name) ?>">
					<?php if (!empty($error['institution_name'])): ?>
						<div class="error"><?= $error['institution_name'] ?></div><?php endif; ?>
				</div>
				<div class="input-box">
					<span class="details">Institution Email</span>
					<input type="email" name="institution_email" value="<?= htmlspecialchars($institution_email) ?>">
					<?php if (!empty($error['institution_email'])): ?>
						<div class="error"><?= $error['institution_email'] ?></div><?php endif; ?>
				</div>
				<div class="input-box">
					<span class="details">Institution Phone</span>
					<input type="text" name="institution_phone" maxlength="10"
						value="<?= htmlspecialchars($institution_phone) ?>">
					<?php if (!empty($error['institution_phone'])): ?>
						<div class="error"><?= $error['institution_phone'] ?></div><?php endif; ?>
				</div>
				<div class="input-box">
					<span class="details">Institution Location</span>
					<input type="text" name="institution_location"
						value="<?= htmlspecialchars($institution_location) ?>">
					<?php if (!empty($error['institution_location'])): ?>
						<div class="error"><?= $error['institution_location'] ?></div><?php endif; ?>
				</div>
			</div>
			<div id="institution-select-supervisor" class="<?= ($role == 'supervisor') ? '' : 'hidden' ?>">
				<div class="input-box">
					<span class="details">Select Institution</span>
					<select name="institution_id_supervisor" id="institution_id_supervisor"
						onchange="handleRoleChange()">
						<option value="" disabled selected>Select an institution</option>
						<?php foreach ($institutions as $inst): ?>
							<option value="<?= $inst['id'] ?>" <?= (isset($institution_id) && $institution_id == $inst['id']) ? 'selected' : ''; ?>>
								<?= htmlspecialchars($inst['name']) ?>
							</option>
						<?php endforeach; ?>
					</select>
					<?php if (!empty($error['institution_id'])): ?>
						<div class="error"><?= $error['institution_id'] ?></div><?php endif; ?>
				</div>
			</div>

			<!-- Supervisor Locations Section (inside the form) -->
			<div id="location-section" class="<?= ($role == 'supervisor' && !empty($institution_id)) ? '' : 'hidden' ?>"
				style="margin-top:20px;">
				<div class="input-box">
					<span class="details">Number of Locations to Scan</span>
					<input type="number" id="num_locations" min="1" max="20" name="num_locations"
						value="<?= isset($_POST['num_locations']) ? intval($_POST['num_locations']) : '' ?>"
						onchange="showLocationInputs()">
					<button type="button" onclick="showLocationInputs()">Set</button>
				</div>
				<div id="locations-container">
					<?php
					if (isset($_POST['location_names']) && is_array($_POST['location_names'])):
						foreach ($_POST['location_names'] as $idx => $loc):
							?>
							<div class="input-box location-input">
								<span class="details">Location Name <?= $idx + 1 ?></span>
								<input type="text" name="location_names[]" value="<?= htmlspecialchars($loc) ?>" required>
								<button type="button" onclick="removeLocationInput(this)">Remove</button>
							</div>
							<?php
						endforeach;
					endif;
					?>
				</div>
				<button type="button" onclick="addMoreLocation()">Add More Location</button>
			</div>
			<!-- End of Supervisor Locations Section -->
			<!-- Institution selection for guard -->
			<div id="institution-select-guard" class="<?= ($role == 'guard') ? '' : 'hidden' ?>">
				<div class="input-box">
					<span class="details">Select Institution</span>
					<select name="institution_id_guard" id="institution_id_guard" onchange="handleRoleChange()">
						<option value="" disabled selected>Select an institution</option>
						<?php foreach ($institutions as $inst): ?>
							<option value="<?= $inst['id'] ?>" <?= (isset($institution_id) && $institution_id == $inst['id']) ? 'selected' : ''; ?>>
								<?= htmlspecialchars($inst['name']) ?>
							</option>
						<?php endforeach; ?>
					</select>
					<?php if (!empty($error['institution_id'])): ?>
						<div class="error"><?= $error['institution_id'] ?></div><?php endif; ?>
				</div>
			</div>

			<!-- id Number -->
			<div id="idnumber-section" class="<?= ($role == 'guard' && !empty($institution_id)) ? '' : 'hidden' ?>"
				style="margin-top:20px;">
				<div class="input-box">
					<span class="details">Id Number</span>
					<input type="text" name="idnumber" value="<?= htmlspecialchars($idnumber) ?>" required>
					<?php if (!empty($error['idnumber'])): ?>
						<div class="error"><?= $error['idnumber'] ?></div><?php endif; ?>
				</div>
			</div>
			<!-- Privacy Policy Section -->
			<div class="privacy-policy-section">
				<p>Do you Agree With our Privacy Policy?</p>
				<input type="radio" id="yes" name="policy" value="yes" <?= (isset($_POST['policy']) && $_POST['policy'] == 'yes') ? 'checked' : ''; ?>>
				<label for="yes">Yes</label>
				<input type="radio" id="no" name="policy" value="no" <?= (isset($_POST['policy']) && $_POST['policy'] == 'no') ? 'checked' : ''; ?>>
				<label for="no">No</label>
				<?php if (!empty($error['policy'])): ?>
					<div class="error"><?= $error['policy'] ?></div><?php endif; ?>
			</div>
			<!-- End of Privacy Policy Section -->
			<!-- Submit Button -->
			<div class="button">
				<input type="submit" name="save" value="Signup">
			</div>
			<!-- End of Submit Button -->
			<!-- Link to login page -->
			<div style="margin-top:15px;text-align:center;">
				Have an account? <a href="login.php">login here</a>
			</div>
		</form>
	</div>
</body>

</html>

<script>
	function addLocationInput(container, idx) {
		var div = document.createElement('div');
		div.className = 'input-box location-input';
		div.innerHTML = `
		<span class="details">Location Name ${idx}</span>
		<input type="text" name="location_names[]" required oninput="sanitizeLocationInput(this)">
		<button type="button" onclick="removeLocationInput(this)">Remove</button>
	`;
		container.appendChild(div);
	}

	// Client-side sanitization for location names
	function sanitizeLocationInput(input) {
		input.value = input.value.replace(/[^a-zA-Z0-9\s\-]/g, '');
	}

	function addMoreLocation() {
		var container = document.getElementById('locations-container');
		addLocationInput(container, container.children.length + 1);
	}

	function removeLocationInput(btn) {
		var container = document.getElementById('locations-container');
		container.removeChild(btn.parentNode);
	}

	window.addEventListener('DOMContentLoaded', function () {
		var roleSelect = document.getElementById('role');
		if (roleSelect && roleSelect.value === 'supervisor') {
			document.getElementById('location-section').classList.remove('hidden');
		}
		document.getElementById('role').addEventListener('change', function () {
			if (this.value === 'supervisor') {
				document.getElementById('location-section').classList.remove('hidden');
			} else {
				document.getElementById('location-section').classList.add('hidden');
				document.getElementById('locations-container').innerHTML = '';
				document.getElementById('num_locations').value = '';
			}
		});
		// Attach sanitization to existing location inputs (if any)
		document.querySelectorAll('input[name="location_names[]"]').forEach(function (input) {
			input.addEventListener('input', function () {
				sanitizeLocationInput(this);
			});
		});
	});


	//handle privacy policy selection
	document.querySelectorAll('input[name="policy"]').forEach(function (input) {
		input.addEventListener('change', function () {
			if (this.value === 'yes') {
				document.querySelector('.privacy-policy-section').classList.remove('error');
			} else {
				document.querySelector('.privacy-policy-section').classList.add('error');
			}
		});
	});

</script>

<style>
	.location-input {
		display: flex;
		align-items: center;
		gap: 10px;
	}

	.location-input input {
		flex: 1;
	}

	.location-input button {
		padding: 4px 10px;
	}
</style>