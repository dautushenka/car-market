{sort}<br /><br />
{pages}
<table class="autos" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<td>�</td>
			<td>���������</td>
			<td>������</td>
			<td>���������� ���������</td>
			<td></td>
			<td>�������</td>
			<td>���������</td>
			<td></td>
			<td>{compare_master}</td>
			<td></td>
			[moder]
			<td></td>
			[/moder] [moder]
			<td>{master_checkbox}</td>
			[/moder]
		</tr>
	</thead>
	<tbody>
		[row_auto]
		<tr>
			<td><a href="{auto_url}" title="����������� ���������">{id}</a></td>
			<td>{cost}</td>
			<td class="{moder_class}"><a href="{auto_url}"
				title="����������� ���������">{mark} {model}</a></td>
			<td>[country]{country} &raquo; [/country][region]{region} &raquo;
			[/region]{city}</td>
			<td>{isset_photo}[photo_count] ({photo_count})[/photo_count]</td>
			<td>[fuel]{fuel}[/fuel]</td>
			<td>{add_date}</td>
			<td>{favorites}</td>
			<td>{compare}</td>
			<td>{send_mail}</td>
			[moder]
			<td>[edit]<img src="{THEME}/images/edit.png"
				title="������������� ����������" border="0">[/edit]</td>
			[/moder] [moder]
			<td>{checkbox}</td>
			[/moder]
		</tr>
		[/row_auto]
	</tbody>
	<tfoot>
		<tr>
			<td colspan="12" style="text-align: right"><input id="compare"
				type="button" value="��������" />[moder]&nbsp;<input type="submit"
				value="�������" />
			</form>
			[/moder]</td>
		</tr>
	</tfoot>
</table>
{pages}<br />
{sort}