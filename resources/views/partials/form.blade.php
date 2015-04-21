<?=Form::open(array('url' => URL::to('/', [], env('production')))) ?>
<div class="input-group">
    <input name="id" type="text" class="form-control promo-input_field" placeholder="Barcode Eg.: 3132727">
    <span class="input-group-btn">
        <button role="button" class="btn btn-success btn-lg" type="submit">Check Now</button>
    </span>
</div>
<?=Form::close() ?>