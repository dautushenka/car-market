$(document).ready(function()
{
	$("#region").find("td:not(:has(a))").click(function()
	{
		if ($(this).parent("tr").next().children("td").is("[colspan='4']"))
		{
			$(this).parent("tr").find("div").toggleClass("minus");
			$(this).parent("tr").next().toggle();
		}
		else
		{
			var id = $(this).parent("tr").attr("id");
			var action = "GetCityEdit";
			$(this).parent("tr").find("div").toggleClass("loader");
			$(this).parent("tr").after("<tr><td></td></tr>").next().hide();
			$(this).parent("tr").next().find("td").attr("colSpan", "4").load(dle_root + "engine/car-market/ajax.php", {'id':id, 'action':action}, function()
			{
			  $(this).parent("tr").show();
				$("div.loader").toggleClass("loader");
				$("#city_" + id + " tr:nth-child(even)").addClass("odd");
				$("#city_" + id + " tr").hover(function()
				{
					$(this).addClass("over");
				}, function()
				{
					$(this).removeClass("over");
				});
			});
			$(this).parent("tr").find("div").toggleClass("minus");
		}
	});
	$("#region tbody").find("td:not(:has(a))").css("cursor", "pointer");
});