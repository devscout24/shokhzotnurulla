<?php
$ds = App\Models\Dealership\Dealer::whereDoesntHave('menus')->get();
$output = get_class($ds) . ' ' . ($ds->count() > 0 ? get_class($ds->first()) : 'empty');
file_put_contents('test.txt', $output);
