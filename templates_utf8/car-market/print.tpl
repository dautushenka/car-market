<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"
	content="text/html; charset=windows-1251" />
__style__
<title>Печать авто</title>
</head>
<body>
<div class="print">
<table width="100%">
	[exist_photo]
	<tr>
		<td colspan="2" width="100%">
		<table width="100%" height="600px">
			<tr>
				<td width="70%">{big_photo}</td>
				<td>{photo_1}<br />
				{photo_2}<br />
				{photo_3}<br />
				{photo_4}<br />
				{photo_5}<br />
				{photo_6}</td>
			</tr>
		</table>
		</td>
	</tr>
	[/exist_photo]
	<tr>
		<td width="40%">Марка автомобиля:</td>
		<td style="padding: 5px;">{mark}</td>
	</tr>
	<tr>
		<td width="40%">Модель:</td>
		<td style="padding: 5px;">{model}</td>
	</tr>
	[country]
	<tr>
		<td width="40%">Страна:</td>
		<td style="padding: 5px;">{country}</td>
	</tr>
	[/country] [region]
	<tr>
		<td width="40%">Регион:</td>
		<td style="padding: 5px;">{region}</td>
	</tr>
	[/region]
	<tr>
		<td width="40%">Город:</td>
		<td style="padding: 5px;">{city}</td>
	</tr>
	[class]
	<tr>
		<td width="40%">Класс автомобиля:</td>
		<td style="padding: 5px;">{class}</td>
	</tr>
	[/class] [basket]
	<tr>
		<td width="40%">Тип кузова:</td>
		<td style="padding: 5px;">{basket}</td>
	</tr>
	[/basket] [state]
	<tr>

		<td width="40%">Состояние:</td>
		<td style="padding: 5px;">{state}</td>
	</tr>
	[/state] [type_motor]
	<tr>
		<td width="40%">Тип двигателя:</td>
		<td style="padding: 5px;">{type_motor}</td>
	</tr>
	[/type_motor] [count_door]
	<tr>
		<td width="40%">Кол-во дверей:</td>

		<td style="padding: 5px;">{count_door}</td>
	</tr>
	[/count_door] [fuel]
	<tr>
		<td width="40%">Потребляемое топливо:</td>
		<td style="padding: 5px;">{fuel}</td>
	</tr>
	[/fuel] [transmission]
	<tr>
		<td width="40%">Коробка передач:</td>
		<td style="padding: 5px;">{transmission}</td>

	</tr>
	[/transmission] [air_conditioner]
	<tr>
		<td width="40%">Кондиционер:</td>
		<td style="padding: 5px;">{air_conditioner}</td>
	</tr>
	[/air_conditioner] [sound_system]
	<tr>
		<td width="40%">Аудиосистема:</td>
		<td style="padding: 5px;">{sound_system}</td>
	</tr>
	[/sound_system] [year]
	<tr>
		<td width="40%">Год выпуска:</td>
		<td style="padding: 5px;">{year}</td>
	</tr>
	[/year] [color]
	<tr>
		<td width="40%">Цвет:</td>
		<td style="padding: 5px;">
		<div style="width: 40px; height: 20px; background-color: {color}"></div>
		</td>
	</tr>
	[/color] [race]
	<tr>
		<td width="40%">Пробег</td>
		<td style="padding: 5px;">{race}</td>
	</tr>
	[/race] [power]
	<tr>
		<td width="40%">Мощность</td>
		<td style="padding: 5px;">{power}</td>
	</tr>
	[/power] [capacity_motor]
	<tr>
		<td width="40%">Объём двигателя</td>
		<td style="padding: 5px;">{capacity_motor}</td>
	</tr>
	[/capacity_motor]
	<tr>
		<td width="40%">Стоимость</td>
		<td style="padding: 5px;">{cost}</td>
	</tr>
	<tr>
		<td style="padding-left: 15px; text-align: left">
		<center><strong>Дополнительные параметры</strong></center>
		</td>
		<td></td>
	</tr>
	<tr>
		<td>{hydraulic_booster} гидроусилитель рулевого управления<br />
		{full_drive} полный привод<br />
		{other_transmission} коробка передач "типроник" или аналогичная<br />
		{window_raiser} электростеклоподъёмники<br />
		{thermo_control} круиз-контроль<br />
		{central_lock} центральный замок<br />
		{signalling} сигнализация<br />
		{xenon_lights} ксеноновые лампы в фарах<br />
		{engine_room_hatch} люк<br />
		{webasto} Webasto или аналог<br />
		{componentry_tuning} элементы тюнинга<br />
		</td>
		<td>{ABS} ABS<br />
		{steadiness} система контроля устойчивости<br />
		{pillow_safety} подушки безопасности<br />
		{invalid} автомобиль для людей с ограниченными возможностями (ручное
		управление и др.)<br />
		{navigation_system} навигационная система<br />
		{mobile} устройство мобильной связи<br />
		{immobilizer} иммобилайзер<br />
		{sensor_parking} датчики парковки<br />
		{leather_salon} кожаный салон<br />
		{warm_seat} подогрев сидений<br />
		{cast_disk} литые диски<br />
		</td>
	</tr>
	[description]
	<tr>
		<td width="40%">Дополнительная информация:</td>
		<td style="padding: 5px;">{description}</td>
	</tr>
	[/description] [contact_person]
	<tr>
		<td width="40%">Контактное лицо</td>
		<td style="padding: 5px;">{contact_person}</td>
	</tr>
	[/contact_person] [phone]
	<tr>
		<td width="40%">Телефон</td>
		<td style="padding: 5px;">{phone}</td>
	</tr>
	[/phone]
	<tr>
		<td width="40%">Размещенно:</td>
		<td style="padding: 5px;">{add_date}</td>
	</tr>
</table>
</div>
</body>
</html>
