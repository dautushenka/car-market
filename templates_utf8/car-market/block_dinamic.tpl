<div class="Block_dimanic">[form_search] Марка автомобиля:<br />
{mark}<br />
Модель:<br />
{model}<br />
[country]Срана:<br />
{country}<br />
[/country] [region]Регион:<br />
{region}<br />
[/region] Город:<br />
{city}<br />
Год выпуска:<br />
{year_min} - {year_max}<br />
Стоимость:<br />
от : <input type="edit" size="8" name="cost_min" /> до <input
	type="edit" name="cost_max" size="8" /> &nbsp; {currency}<br />
С фото <input type="checkbox" name="isset_photo" value="1" />&nbsp;<input
	type="submit" value="Поиск" style="border: 1px #000 solid" />
</form>
[/form_search] [list] <span style="font-size: 16px; font-weight: bold">{name}</span>
<ul>
	[row_item]
	<li>{item}</li>
	[/row_item]
</ul>
[/list]</div>
