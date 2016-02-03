<style type="text/css">
.block_last_auto a,.block_last_auto a:visited {
	text-decoration: none;
	color: #266FA4;
	font-weight: bold;
}

.block_last_auto a:hover {
	text-decoration: underline;
	color: #0099FF;
	font-weight: bold;
}

a>img {
	text-decoration: none;
	font-weight: bold;
	border: 0px;
}
</style>
<table width="100%" class="block_last_auto">
	<tr>
		[row_auto separator="
	</tr>
	<tr>
		" count="1"]
		<td width="20%" style="border-top: 2px solid #000000">
		<div style="font-size: 18px; padding: 5px;"><a href="{auto_url}">{mark}
		{model}</a></div>
		{photo}<br />
		[year]<b>Год выпуска:</b> {year}<br />
		[/year] <b>Стоимость:</b> <font color="#FF0000">{cost}</font><br />
		<b>Автомобиль находится в:</b> [country]{country} &raquo;
		[/country][region]{region} &raquo; [/region]{city}<br />
		[power]<b>Мощность:</b> {power}<br />
		[/power] [race]<b>Пробег:</b> {race} тыс. км.<br />
		[/race] [fuel]<b>Топливо:</b> {fuel}[/fuel]
		<div style="padding-top: 10px;">Размещенно: <strong>{add_date}</strong>
		<hr />
		</div>
		</td>
		[/row_auto]
	</tr>
</table>
