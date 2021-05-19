<div class="tabs-container">
    <ul class="nav nav-tabs" role="tablist">
        <li><a class="nav-link payTab" data-toggle="tab" href="#nicasia"> Nic Asia</a></li>
        <li><a class="nav-link payTab" data-toggle="tab" href="#imepay"> ImePay</a></li>
        <li><a class="nav-link payTab" data-toggle="tab" href="#khalti"> Khalti</a></li>
        <li><a class="nav-link payTab" data-toggle="tab" href="#prabhupay"> PrabhuPay</a></li>
        <li><a class="nav-link payTab" data-toggle="tab" href="#esewa"> Esewa</a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" id="nicasia" class="tab-pane">
            <div class="panel-body" style="width: 100%;margin-left: 0%;">
                @include('partials.nic-asia')
            </div>
        </div>
        <div role="tabpanel" id="imepay" class="tab-pane">
            <div class="panel-body" style="width: 100%;margin-left: 0%;">
                @include('partials.imepay')
            </div>
        </div>
        <div role="tabpanel" id="khalti" class="tab-pane">
            <div class="panel-body" style="width: 100%;margin-left: 0%;">
                @include('partials.khalti')
            </div>
        </div>
        <div role="tabpanel" id="prabhupay" class="tab-pane">
            <div class="panel-body" style="width: 100%;margin-left: 0%;">
                @include('partials.prabhupay')
            </div>
        </div>
        <div role="tabpanel" id="esewa" class="tab-pane">
            <div class="panel-body" style="width: 100%;margin-left: 0%;">
                @include('partials.esewa')
            </div>
        </div>
    </div>
</div>
