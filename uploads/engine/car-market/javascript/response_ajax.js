	$("a.mail_friend").click(function()
	{
		var auto_url = $(this).attr("href");
		$("#send_form textarea").val($("#send_form textarea").val().replace(/http:.*?id=[0-9]+/, auto_url));
		$("#send_form textarea").val($("#send_form textarea").val().replace(/http:.*?auto-[0-9]+.html/, auto_url));
		$("#send_form textarea").val($("#send_form textarea").val().replace(/__auto_link__/, auto_url));
		$.blockUI({message:$("#send_mail")});
	});
    
    $("#master").click(function()
	{
		if ($(this).is(":checked"))
			$("#auto_form input:checkbox").attr("checked", "ckecked");
		else
			$("#auto_form input:checkbox").removeAttr("checked");
	});
	
	$("#compare_master").click(function()
	{
		compare = new Array();
		
		if (compareAll)
		{
			compareAll = false;
			$(this).attr("src", dle_root + "templates/" + dle_skin + "/car-market/images/compare_checked.gif")
			$(".compare").each(function()
			{
				$(this).attr("src", dle_root + "templates/" + dle_skin + "/car-market/images/compare_unchecked.gif")
			});
		}
		else
		{
			compareAll = true;
			$(this).attr("src", dle_root + "templates/" + dle_skin + "/car-market/images/compare_unchecked.gif")
			$(".compare").each(function()
			{
				compare.push($(this).attr("id"));
				$(this).attr("src", dle_root + "templates/" + dle_skin + "/car-market/images/compare_checked.gif")
			});
		}
	});
	
	$("img.compare").click(function()
	{
		id = $(this).attr("id");
		
		if (!id)
			return false;
			
		var index = FindIndex(compare, id);
	
		if (compare.length > 0 && index != -1)
		{
			compare.splice(index, 1);
			$(this).attr("src", dle_root + "templates/" + dle_skin + "/car-market/images/compare_unchecked.gif");
		}
		else
		{
			compare.push(id);
			$(this).attr("src", dle_root + "templates/" + dle_skin + "/car-market/images/compare_checked.gif");
		}
	});
	
	$("#compare").click(function()
	{
		if (compare.length < 2)
		{
			$.blockUI({message:compare_error});
			window.setTimeout($.unblockUI, 2500);
			return false;
		}
		window.open(dle_root + "engine/car-market/compare.php?id=" + compare.toString(), '_blank', "toolbar=yes, location=no, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=yes, copyhistory=no, width=600, height=600");
		
	});
	
	$("table.autos tbody tr:nth-child(even)").addClass('even');
	$("table.autos_modern tbody td:nth-child(even)").addClass('even');
	$("table.autos tbody tr:not(:has(input:button, input:submit))").hover(function()
	{
		$(this).toggleClass("over");
	}, function()
	{
		$(this).toggleClass("over");
	});
	$("a.ajax_link").click(function()
	{
		BlockContent($("#auto-content"));
		url = $(this).attr("href");
		$("#auto-content").load(url, function()
        {
            $("a.ajax_link").unbind("click");
            $.getScript(dle_root + "engine/car-market/javascript/response_ajax.js");
            return false;
        });
		
		return false;
	});