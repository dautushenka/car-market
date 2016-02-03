<link rel="stylesheet" href="{THEME}/car-market/css/tabs.css"
	type="text/css" media="screen">
<script
	src="/engine/car-market/javascript/ui.core.js" type="text/javascript"></script>
<script
	src="/engine/car-market/javascript/ui.tabs.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function()
{
	$("#tabs").tabs(
	{
		fx:
		{ 
	        opacity: "toggle" ,
			 duration: 'fast'
    	}	 
	});
});
</script>
<div style="margin: 5px; margin-top: 15px;">
<ul id="tabs">
	<li><a href="#my_auto">Мои авто</a></li>
	<li><a href="#favorites">Избранное</a></li>
	<li><a href="#settings">Настройки</a></li>
</ul>
<div id="my_auto">
<div style="padding: 10px; font-size: 20px;">Мои авто</div>
<table class="autos" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<td></td>
			<td>№</td>
			<td>Стоимость</td>
			<td>Модель</td>
			<td>Автомобиль находится</td>
			<td>Топливо</td>
			<td>Размещено</td>
			<td>Статус</td>
			[allow_del]
			<td>{master_checkbox}</td>
			[/allow_del]
		</tr>
	</thead>
	<tbody>
		[row_auto]
		<tr>
			<td>[edit]<img src="{THEME}/images/edit.png"
				title="Редактировать объявление" border="0">[/edit]</td>
			<td><a href="{auto_url}" title="Просмотреть полностью">{id}</a></td>
			<td>{cost}</td>
			<td><a href="{auto_url}" title="Просмотреть полностью">{mark} {model}</a></td>
			<td>[country]{country} &raquo; [/country][region]{region} &raquo;
			[/region]{city}</td>
			<td>[fuel]{fuel}[/fuel]</td>
			<td>{add_date}</td>
			<td>{status}</td>
			[allow_del]
			<td>{checkbox}</td>
			[/allow_del]
		</tr>
		[/row_auto]
	</tbody>
	[allow_del]
	<tr>
		<td colspan="8" style="text-align: right"><input type="submit"
			value="Удалить" />
		</form>
		</td>
	</tr>
	[/allow_del]
</table>
{pages}</div>
<div id="favorites" style="display: none">
<div style="padding: 10px; font-size: 20px;">Избранное</div>
<table class="autos" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<td></td>
			<td>№</td>
			<td>Стоимость</td>
			<td>Модель</td>
			<td>Автомобиль находится</td>
			<td>Топливо</td>
			<td>Размещено</td>
			<td>{compare_master}</td>
		</tr>
	</thead>
	<tbody>
		[row_favorites]
		<tr>
			<td>{favorites}</td>
			<td><a href="{auto_url}" title="Просмотреть полностью">{id}</a></td>
			<td>{cost}</td>
			<td><a href="{auto_url}" title="Просмотреть полностью">{mark} {model}</a></td>
			<td>[country]{country} &raquo; [/country][region]{region} &raquo;
			[/region]{city}</td>
			<td>[fuel]{fuel}[/fuel]</td>
			<td>{add_date}</td>
			<td>{compare}</td>
		</tr>
		[/row_favorites]
	</tbody>
	<tr>
		<td colspan="8" style="text-align: right"><input id="compare"
			type="button" value="Сравнить" /></td>
	</tr>
</table>
</div>

<div id="settings" style="display: none">
<form action="" method="POST" id="form_settings">
<div style="padding: 10px; font-size: 20px;">Фильтр по умолчанию</div>
<table width="100%" style="margin-bottom: 10px;">
	<tr>
		<td style="text-align: right; width: 50%">Марка автомобиля:</td>
		<td style="padding: 5px; text-align: left">{mark}</td>
	</tr>
	<tr>
		<td style="text-align: right; width: 50%">Модель:</td>
		<td style="padding: 5px; text-align: left">{model}</td>
	</tr>
	[country]
	<tr>
		<td style="text-align: right; width: 50%">Срана:</td>
		<td style="padding: 5px; text-align: left">{country}</td>
	</tr>
	[/country] [region]
	<tr>
		<td style="text-align: right; width: 50%">Регион:</td>
		<td style="padding: 5px; text-align: left">{region}</td>
	</tr>
	[/region]
	<tr>
		<td style="text-align: right; width: 50%">Город:</td>
		<td style="padding: 5px; text-align: left">{city}</td>
	</tr>
	<tr>
		<td style="text-align: right; width: 50%">Стоимость:</td>
		<td style="padding: 5px; text-align: left">от : <input type="edit"
			size="8" value="{cost_min}" name="cost_min" /> до <input type="edit"
			value="{cost_max}" name="cost_max" size="8" /> &nbsp; {currency}</td>
	</tr>
	<tr>
		<td style="text-align: right; width: 50%">Показываеть дополнительно
		стоимость в:</td>
		<td style="padding: 5px; text-align: left">{currency_defalut}</td>
	</tr>
	<tr>
		<td style="text-align: right; width: 50%">Сортировать по:</td>
		<td style="padding: 5px; text-align: left">{sort}</td>
	</tr>
	<tr>
		<td style="text-align: right; width: 50%">Только с фото</td>
		<td style="padding: 5px; text-align: left">{isset_photo}</td>
	</tr>
	<tr>
		<td style="text-align: center" colspan="2"><input type="submit"
			value="Сохранить" />
		</form>
		</td>
	</tr>
</table>

</div>
</div>
