
<?php
$types = get_types();
//vd($p);
?>
<form>
<div class="uk-child-width-expand@s uk-text-center" uk-grid>
    <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-body">

            <div class="uk-width-1-3">
                <div class="uk-margin">
                    <select class="uk-select select-brand">
                        <option selected value="1">Tamlite</option>
                        <option value="2">xcite</option>
                    </select>
                </div>
            </div>

            <div class="uk-width-1-3">
                <div class="uk-margin">
                    <div id="target-select-product-type" class="uk-margin">
                        <select class="uk-select select-product-type">
                            <option selected value="">Select a Product Type</option>
                            <?php foreach ($types as $p) {
                                echo "<option value='$p->type_slug_pk'>$p->type_name</option>";
                            } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="uk-width-1-3">
                <div class="uk-margin">
                    <div id="target-select-product" class="uk-margin">
                        <select class="uk-select select-product">
                            <option selected>Select a Product</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="uk-width-1-3">
                <div class="uk-margin">
                    <div id="target-select-sku" class="uk-margin">
                        <select class="uk-select select-sku">
                            <option selected>Select a SKU</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="uk-width-1-3">
        </div>
        <div class="uk-width-1-3">
        </div>

    </div>
</div>
</form>

<!-- Templates -->
<script id="tmp-select-product-type" type="x-tmpl-mustache">
<select class="uk-select select-product-type">
{{#skus}}
    <option value="{{type_slug_pk}}">{{type_name}}</option>
{{/skus}}
</select>
</script>
<script id="tmp-select-product" type="x-tmpl-mustache">
<select class="uk-select select-product">
{{#skus}}
    <option value="{{product_slug_pk}}">{{hrslug}}</option>
{{/skus}}
</select>
</script>
<script id="tmp-select-sku" type="x-tmpl-mustache">
<select class="uk-select select-sku">
{{#skus}}
    <option>{{.}}</option>
{{/skus}}
</select>
</script>


