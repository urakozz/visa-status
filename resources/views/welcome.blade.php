@extends('layout.promo')
@section('content')
    <div class="promo-container_title">Your Visa Status here</div>
    <div class="promo-container_quote promo-container_quote__padding">
        Check if your German National Visa ready
    </div>
    <div class="promo-container_form">
        <?=Form::open(array('url' => '/')) ?>
        <div class="input-group">
            <input name="id" type="text" class="form-control promo-input_field" placeholder="Barcode Eg.: 3132727">
            <span class="input-group-btn">
                <button role="button" class="btn btn-success btn-lg" type="submit">Check Now</button>
            </span>
        </div>

        <?=Form::close() ?>
    </div>
@stop
