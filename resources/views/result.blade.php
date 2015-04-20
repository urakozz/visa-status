@extends('layout.promo')
@section('content')
    <div class="result-status_title result-status_title__{{$data->getColor()}}">
        {{ $data->getStatusMessage() }}
    </div>
    <div class="result-status_description">
        @if($data->isSuccess())
            <div class="description-summary">
                <p>Your details are here:</p>

                <p>{{ $data->getMessage() }}</p>
            </div>
            <div class="description-details">
                <span class="result-description_title">Document details:</span>

                @foreach($data->getDocuments() as $code=>$desc)
                    <span class="list-group-item">
                        <h4 class="list-group-item-heading">{{$code}}</h4>
                        <p class="list-group-item-text">{{$desc}}</p>
                    </span>
                @endforeach

            </div>
            <div class="description-additional">
                <p>Заявители со следующими штрих-кодами могут прийти с загранпаспортом для получения визы с
                понедельника по пятницу с 08:00 до 09:00 ч. в Визовый отдел Посольства.
                <p>После 09:00 паспорта не принимаются.
                <p>Виза, как правило, выдаётся в тот же день на кассе (с понедельника по четверг с 14:30 до 15:30, в
                пятницу с 13:30 до 14:00)
                <p>Просьба учитывать приведённую ниже информацию о необходимых для получения визы документах!
                <p>Паспорт на визирование может подать третье лицо при предъявлении доверенности от заявителя в
                свободной форме.
                <p>Виза может быть выдана только в том случае, если Ваш паспорт действителен ещё минимум 6 месяцев.
                Если виза выдается на весь запланированный период пребывания, например, в случаях обмена студентами или
                персоналом, паспорт должен быть действителен минимум в течение всего периода пребывания плюс
                дополнительно 3 месяца.
            </div>
        @else
            <div class="description-summary">
                <p>Check your number <b>{{$data->getId()}}</b> is correct or try later
            </div>

        @endif
    </div>

    <div class="promo-container_form promo-container_form__padding">
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
