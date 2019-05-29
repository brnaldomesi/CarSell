<?php
if (!defined('ABSPATH')) exit;

global $mpsl_options;

$view = isset($_GET['view']) ? $_GET['view'] : null;
$id = isset($_GET['id']) ? $_GET['id'] : null;

//$mpsl_options->override($id, false, false);

?>


<style type="text/css">
    body {
        background-color: #f1f1f1;
    }
</style>


<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>Header</h3>
        </div>
    </div>
    <?php
        $mpsl_options->render($view);
    ?>
</div>