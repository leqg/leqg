<?php
	if (isset($_GET['pass'])) {
		echo password_hash($_GET['pass'], PASSWORD_BCRYPT);
	}
?>