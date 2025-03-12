<script src="/js/pdf.js/pdf.mjs" type="module"></script>
<script src="/js/plan.mjs" type="module"></script>

<?php

$project_slug = get_uri_part(2);

$p = get_project($project_slug);

//$types = get_types();

include ('./partials/tables-side.php');

?>

<div id="plan" class="uk-width-1-1">

    <div class="uk-width-1-1" uk-grid>

        <div class="uk-width-1-1 uk-margin">
            <span title="Copy Project" alt="Copy Project" id="copy-project" class="uk-icon uk-align-right uk-margin-remove" uk-icon="icon: copy; ratio: 2;"></span>
            <button class="uk-button uk-align-right uk-button-primary uk-hidden@xl " type="button" uk-toggle="target: #offcanvas-sidebar">Manage Project</button>
        </div>



        <canvas id="the-canvas"></canvas>

    </div>

</div>
