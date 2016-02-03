<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"
	content="text/html; charset=windows-1251" />
__style__ __script__ __subhead__
<title>Сравнение авто</title>
</head>
<body>
<div style="margin: 5px; margin-top: 15px; display: inline">
<table class="autos" cellpadding="0" cellspacing="0">
	<thead>
		<tr style="">
			<td width="200px"><a href="#" id="equal">Показать/Скрыть одинаковые
			параметры</a></td>
			<td class="__cost__">Стоимость</td>
			<td class="__year__">Год выпуска</td>
			<td class="__race__">Пробег</td>
			<td class="__power__">Мощность</td>
			<td class="__fuel__">Топливо</td>
			<td class="__capacity_motor__">Объём двигателя</td>
			<td class="__type_motor__">Тип двигателя</td>
			<td class="__color__">Цвет</td>
			<td class="__basket__">Тип кузова</td>
			<td class="__air_conditioner__">Кондиционер</td>
			<td class="__sound_system__">Аудиосистема</td>
			<td class="__transmission__">Коробка передач</td>
			<td class="__add_date__">Размещенно</td>
		</tr>
	</thead>
	<tbody>
		[row_auto]
		<tr>
			<td>{photo_1}<br />
			{favorites}&nbsp;<a href="{auto_url}" title="Просмотреть полностью">{mark}
			{model}</a>&nbsp;<img class="close_compare_auto"
				title="Удалить из сравнения"
				src="{THEME}/car-market/images/close_compare.png"<br />
	[country]{country} &raquo; [/country][region]{region} &raquo; [/region]{city}
	</td>
			<td class="__cost__">{cost}</td>
			<td class="__year__">{year}</td>
			<td class="__race__">{race}</td>
			<td class="__power__">{power}</td>
			<td class="__fuel__">{fuel}</td>
			<td class="__capacity_motor__">{capacity_motor}</td>
			<td class="__type_motor__">{type_motor}</td>
			<td class="__color__">[color]
			<div style="width: 40px; height: 20px; background-color: {color}"></div>
			[/color]</td>
			<td class="__basket__">{basket}</td>
			<td class="__air_conditioner__">{air_conditioner}</td>
			<td class="__sound_system__">{sound_system}</td>
			<td class="__transmission__">{transmission}</td>
			<td class="__add_date__">{add_date}</td>
		</tr>
		[/row_auto]
	</tbody>
</table>
<div align="right"><a style="text-decoration: none"
	href="javascript:window.close();" title="Закрыть"><img
	src="{THEME}/car-market/images/close.png" border="0" /> Закрыть</a></div>
</div>
</body>
</html>


