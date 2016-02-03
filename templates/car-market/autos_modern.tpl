{sort}<br /><br />
{pages}
<table width="100%" class="autos_modern">
	<tr>
		[row_auto separator="
	</tr>
	<tr>
		" count="2"]
		<td width="20%" align="right">{photo}</td>
		<td width="30%">
		<div class="{moder_class}" style="font-size: 18px; padding: 5px;"><a
			href="{auto_url}">{mark} {model}</a></div>
		[year]<b>Год выпуска:</b> {year}<br />
		[/year] <b>Стоимость:</b> <font color="#FF0000">{cost}</font><br />
		<b>Автомобиль находится в:</b> [country]{country} &raquo;
		[/country][region]{region} &raquo; [/region]{city}<br />
		[power]<b>Мощность:</b> {power}<br />
		[/power] [race]<b>Пробег:</b> {race}<br />
		[/race] [fuel]<b>Топливо:</b> {fuel}[/fuel]
		<div style="padding-top: 10px;">Размещенно: <strong>{add_date}</strong><br />
		{favorites}&nbsp;&nbsp;{compare}&nbsp;&nbsp;{send_mail} [edit]<br />
		<img src="{THEME}/images/edit.png" title="Редактировать вакансию"
			border="0">[/edit]&nbsp;&nbsp;[moder]{checkbox}[/moder]&nbsp;{info}</div>
		</td>
		[/row_auto]
	</tr>
	<tr>
		<td colspan="11" style="text-align: right"><input id="compare"
			type="button" value="Сравнить" />[moder]&nbsp;<input type="submit"
			value="Удалить" />
		</form>
		[/moder]</td>
	</tr>
</table>
{pages}
<br />{sort}
