<?php
use App\Models\Prize;
$current_probability = floatval(Prize::sum('probability'));
$remaining_probability = 100 - $current_probability;
?>

<div class="alert alert-info alert-dismissible fade show" role="alert">
    <h6>Sum of all prizes probability must be 100%. Currently its {{ $current_probability }}% You have yet to add {{ $remaining_probability }}% to the prize.</h6>
</div> 


