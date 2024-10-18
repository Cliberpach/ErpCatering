<style>
.switch {
	position: relative;
	display: block;
	vertical-align: top;
	width: 140px;
	height: 30px;
	padding: 3px;
	margin: 0 10px 10px 0;
	background: linear-gradient(to bottom, #eeeeee, #FFFFFF 25px);
	background-image: -webkit-linear-gradient(top, #eeeeee, #FFFFFF 25px);
	border-radius: 18px;
	box-shadow: inset 0 -1px white, inset 0 1px 1px rgba(0, 0, 0, 0.05);
	cursor: pointer;
	box-sizing:content-box;
}
.switch-input-ambiente-greenter {
	position: absolute;
	top: 0;
	left: 0;
	opacity: 0;
	box-sizing:content-box;
}
.switch-label {
	position: relative;
	display: block;
	height: inherit;
	font-size: 10px;
	text-transform: uppercase;
	background: #eceeef;
	border-radius: inherit;
	box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.12), inset 0 0 2px rgba(0, 0, 0, 0.15);
	box-sizing:content-box;
}
.switch-label:before, .switch-label:after {
	position: absolute;
	top: 50%;
	margin-top: -.5em;
	line-height: 1;
	-webkit-transition: inherit;
	-moz-transition: inherit;
	-o-transition: inherit;
	transition: inherit;
	box-sizing:content-box;
}
.switch-label:before {
	content: attr(data-off);
	right: 11px;
	color: #aaaaaa;
	text-shadow: 0 1px rgba(255, 255, 255, 0.5);
}
.switch-label:after {
	content: attr(data-on);
	left: 11px;
	color: #FFFFFF;
	text-shadow: 0 1px rgba(0, 0, 0, 0.2);
	opacity: 0;
}
.switch-input-ambiente-greenter:checked ~ .switch-label {
	background: #E1B42B;
	box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), inset 0 0 3px rgba(0, 0, 0, 0.2);
}
.switch-input-ambiente-greenter:checked ~ .switch-label:before {
	opacity: 0;
}
.switch-input-ambiente-greenter:checked ~ .switch-label:after {
	opacity: 1;
}
.switch-handle {
	position: absolute;
	top: 4px;
	left: 4px;
	width: 28px;
	height: 28px;
	background: linear-gradient(to bottom, #FFFFFF 40%, #f0f0f0);
	background-image: -webkit-linear-gradient(top, #FFFFFF 40%, #f0f0f0);
	border-radius: 100%;
	box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
}
.switch-handle:before {
	content: "";
	position: absolute;
	top: 50%;
	left: 50%;
	margin: -6px 0 0 -6px;
	width: 12px;
	height: 12px;
	background: linear-gradient(to bottom, #eeeeee, #FFFFFF);
	background-image: -webkit-linear-gradient(top, #eeeeee, #FFFFFF);
	border-radius: 6px;
	box-shadow: inset 0 1px rgba(0, 0, 0, 0.02);
}
.switch-input-ambiente-greenter:checked ~ .switch-handle {
	left: 114px;
	box-shadow: -1px 1px 5px rgba(0, 0, 0, 0.2);
}
 
/* Transition
========================== */
.switch-label, .switch-handle {
	transition: All 0.3s ease;
	-webkit-transition: All 0.3s ease;
	-moz-transition: All 0.3s ease;
	-o-transition: All 0.3s ease;
}
</style>


<form action="" id="formActualizarConfiguracion" method="post">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="row" style="border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <label for="" style="font-weight: bold;">
                        <img width="40" height="40" src="{{asset('img/icons/web/web1.png')}}" alt="coins"/> AMBIENTE GREENTER
                    </label>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 d-flex align-items-center">
                    <label class="switch config_ambiente_greenter" style="margin-bottom:0px;">
                        <input 
                            @if ($configuraciones[0]->propiedad === 'PRODUCCION')
                                checked
                            @endif 
                        class="switch-input-ambiente-greenter" type="checkbox" />
                        <span class="switch-label" data-on="PRODUCCION" data-off="BETA"></span> 
                        <span class="switch-handle"></span> 
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>