<?php
$event = Event::create($_GET['contact']);
$event = new Event($event);
echo $event->json();