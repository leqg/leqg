<?php
$campaign = new Campaign($_GET['campagne']);
echo json_encode($campaign->list_events());
