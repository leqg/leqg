<?php
	// On fait un git status du répertoire pour voir où on en est
	exec('git status', $output);
	echo '<pre>'; print_r($output); echo '</pre>';
	unset($output);
	
	// On tente le git pull du répertoire
	exec('git pull', $output);
	echo '<pre>'; print_r($output); echo '</pre>';
	unset($output);