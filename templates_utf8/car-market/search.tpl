
<table width="100%" style="margin-bottom: 10px;">
	<tr>
		<td colspan="2" align="left" style="padding: 10px; font-size: 20px;">Расширенный
		поиск</td>
	</tr>
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
		<td width="40%">Срана:</td>
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
	<tr>
		<td width="40%">Класс автомобиля:</td>
		<td style="padding: 5px;">{class}</td>
	</tr>
	<tr>
		<td width="40%">Тип кузова:</td>
		<td style="padding: 5px;">{basket}</td>
	</tr>
	<tr>

		<td width="40%">Состояние:</td>
		<td style="padding: 5px;">{state}</td>
	</tr>
	<tr>
		<td width="40%">Тип двигателя:</td>
		<td style="padding: 5px;">{type_motor}</td>
	</tr>
	<tr>
		<td width="40%">Кол-во дверей:</td>

		<td style="padding: 5px;">{count_door}</td>
	</tr>
	<tr>
		<td width="40%">Потребляемое топливо:</td>
		<td style="padding: 5px;">{fuel}</td>
	</tr>
	<tr>
		<td width="40%">Коробка передач:</td>
		<td style="padding: 5px;">{transmission}</td>

	</tr>
	<tr>
		<td width="40%">Кондиционер:</td>
		<td style="padding: 5px;">{air_conditioner}</td>
	</tr>
	<tr>
		<td width="40%">Аудиосистема:</td>
		<td style="padding: 5px;">{sound_system}</td>
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
	<tr>
		<td width="30%">Год выпуска</td>
		<td style="padding: 5px;">{year_min} - {year_min}</td>
	</tr>
	<tr>
		<td width="30%">Пробег</td>
		<td style="padding: 5px;">до : <input type="edit" size="8" name="race" />
		тыс. км.</td>
	</tr>
	<tr>
		<td width="30%">Объём двигателя</td>
		<td style="padding: 5px;">от : <input type="edit" size="8"
			name="capacity_motor_min" /> до <input type="edit"
			name="capacity_motor_max" size="8" /> куб.см</td>
	</tr>
	<tr>
		<td width="30%">Мощность</td>
		<td style="padding: 5px;">от : <input type="edit" size="8"
			name="power_min" /> до <input type="edit" name="power_max" size="8" />
		кВатт</td>
	</tr>
	<tr>
		<td width="30%">Стоимость</td>
		<td style="padding: 5px;">от : <input type="edit" size="8"
			name="cost_min" /> до <input type="edit" name="cost_max" size="8" />
		&nbsp; {currency}</td>
	</tr>
	<tr>
		<td width="30%">С фото</td>
		<td style="padding: 5px;"><input type="checkbox" name="isset_photo"
			value="1" /></td>
	</tr>
	<tr>
		<td width="40%">Показать</td>
		<td style="padding: 5px;">{sel_count}</td>
	</tr>
</table>
<input
	type="submit" value="Поиск" class="bbcodes_poll" />
</form>
