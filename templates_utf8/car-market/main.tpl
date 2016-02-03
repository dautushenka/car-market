<div>{head}
<div id="auto-content">[main_country_region] <span
	style="font-size: 20px; font-weight: bold">{name}</span>
<hr />
<table width="100%">
	<tr>
		[row_items separator="
	</tr>
	<tr>
		" count="4"]
		<td>{item}</td>
		[/row_items]
	</tr>
</table>
<hr />
<br />
<br />
[/main_country_region]
<table width="100%" class="autos_modern">
	<tr>
		[row_auto separator="
	</tr>
	<tr>
		" count="2"]
		<td width="20%" align="right">{photo_1}</td>
		<td width="30%">
		<div style="font-size: 18px; padding: 5px;"><a href="{auto_url}">{mark}
		{model}</a></div>
		[year]<b>Год выпуска:</b> {year}<br />
		[/year] <b>Стоимость:</b> <font color="#FF0000">{cost}</font><br />
		<b>Автомобиль находится в:</b> [country]{country} &raquo;
		[/country][region]{region} &raquo; [/region]{city}<br />
		[power]<b>Мощность:</b> {power}<br />
		[/power] [race]<b>Пробег:</b> {race}<br />
		[/race] [fuel]<b>Топливо:</b> {fuel}[/fuel]
		<div style="padding-top: 10px;">Размещенно: <strong>{add_date}</strong>
		[edit]<br />
		<img src="{THEME}/images/edit.png" title="Редактировать вакансию"
			border="0">[/edit]&nbsp;{info}</div>
		</td>
		[/row_auto]
	</tr>
</table>
<br />
{stats}</div>
</div>
