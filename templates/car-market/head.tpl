<div class="head">
<div
	style="color: #0033CC; font-size: 20px; display: inline; vertical-align: top">Фильтр</div>
[rss] &nbsp;<img border="0" src="{THEME}/car-market/images/rss.gif"
	title="RSS объявлений по текущим параметрам поиска" />[/rss]<br />
<table style="display:inline">
    <tr>
        [country]<td>Страна: {country}&nbsp;</td>[/country]
        [region]<td>Регион:{region}&nbsp;</td>[/region]
        <td>Город: </td><td>{city} </td>
    </tr>
</table>
&nbsp;Пробег: до <input
	type="text" name="race" value="{race}" size="8" /> тыс. км.
{isset_photo} С фото<br />
<br />
<table style="display:inline">
   <tr>
    <td>Марка:</td>
    <td>{mark}</td>
    <td>&nbsp;Модель:</td>
    <td>{model}</td>
</tr>
</table>
    &nbsp;&nbsp;Стоимость: <input
	type="text" name="cost_min" value="{cost_min}" size="8" /> - <input
	type="text" name="cost_max" value="{cost_max}" size="8" />&nbsp;{currency}
&nbsp; <input type="submit" value="Фильтровать" /><br />
<br />
[view] &nbsp;&nbsp;&nbsp; <b>Вид</b>
[table]Табличный[/table]&nbsp;/&nbsp;[modern]Расширенный[/modern][/view]
</div>
</form>
