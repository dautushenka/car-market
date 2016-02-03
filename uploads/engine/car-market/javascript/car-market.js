// JavaScript Document
function ShowMenu(id, allow_site)
{

var menu = new Array();

if (edit_auto[id] != null)
{
	allow_site = (edit_auto[id])?0:1;
}
if (allow_site == 0)
	auto_allow = auto_allow_no;
else
	auto_allow = auto_allow_yes;	

menu[0]='<a href="' + dle_root + '?do=car-market&action=edit&id=' + id + '" target="_blank">' + auto_edit + '</a>';
menu[1]='<a onclick="auto_allow_site(\'' + id + '\', '+allow_site+'); return false;" href="#">' + auto_allow + '</a>';

return menu;
};

var Element = '';
var edit_auto = new Array();
var id = 0;
var compareAll = false;
var compare = new Array();
var SelectedModels = new Array();
var SelectedCities = new Array();

if (typeof(admin) ==  undefined)
{
    var admin = false;
}

function auto_allow_site(id, allow)
{
	if (allow == 0)
		$(".id" + id).addClass("moder_old_auto");
	else
	{
		$(".id" + id).removeClass("moder_new_auto");
		$(".id" + id).removeClass("moder_old_auto");
	}
	edit_auto[id] = allow;
	$.post(ajax_url, {'action':'allow_site', 'id':id, 'allow':allow});
	
	return false;
}

function FillSelect(xml)
{
	var length = $('item', xml).length;
	$(Element).text('').disabled;
	for (i=0; i<length; i++)
	{
		id = $('item', xml).eq(i).attr('id');
		value = $('item', xml).eq(i).text();
		$(document.createElement('option')).attr('value', id).text(value).appendTo($(Element));
	}

	if (!(length == 1 && $(Element).val() == 0))
		$(Element).removeAttr('disabled');
		
	$("label.loader").remove();
}

function GetValues(id, action, search)
{
	if (!search)
		search = 0;
		
	$(Element).attr('disabled', 'disabled');
	$(Element).before("<label style=\"position:absolute\" class='loader'> </label>");
	$.post(ajax_url, {'id':id, 'action':action, 'search':search}, FillSelect, 'xml');
}

function del_image(auto_id, image_id)
{
	var image_id;
	$("#photo").disabled = false;
	$("#image_"+image_id).prepend("<label style=\"position:absolute\" class='loader'> </label>");
	$.post(ajax_url, {'action':'DelImage', 'id':image_id, 'auto_id':auto_id}, function (data)
	{
		if (data == 'denied')
		{
			$("#image_"+image_id).find("label").remove();
			alert('Access Denied');
		}
		else
		{
		    $("#image_"+image_id).remove();
		    photos--;
		    if (allow_max_photo > photos)
		    {
		        $('#uploads').show();
		        $('#uploadsUploader').show();
		        $('#max_photo').remove();
		    }
		    $('#form_add').find('input:hidden[value="' + image_id + '"]').remove();
		}
	});
	
}

function AddPhoto(responseJSON, auto_id, checked)
{
    var td = document.createElement('td');
    td.id = 'image_' + responseJSON.image_id;
    var a = document.createElement('a');
    a.href = responseJSON.image_url;
    a.className = "image";
    var img = document.createElement('img');
    img.src = responseJSON.image_th_url;
    img.border = 0;
    a.appendChild(img);
    
    var a_del = document.createElement('a');
    a_del.href = 'javascript:del_image(' + auto_id + ', ' + responseJSON.image_id + ')';
    a_del.appendChild(document.createTextNode("[" + del_txt + "]"));
    
    var radio = document.createElement('input');
    radio.name = 'main_photo';
    radio.value = responseJSON.image_id;
    radio.type = "radio";
    
    if (checked)
    {
        radio.checked = true;
    }
    
    td.appendChild(a);
    td.appendChild(document.createElement('br'));
    td.appendChild(radio);
    td.appendChild(document.createTextNode(main_txt));
    td.appendChild(document.createElement('br'));
    td.appendChild(a_del);
    
    if (photos%4 == 0)
    {
       var tr = document.createElement('tr');
       tr.appendChild(td);
       $('#images').append(tr);
    }
    else
    {
       $('#images tr:last').append(td);
    }
    
    $('#form_add').append("<input type='hidden' name='images[]' value='" + responseJSON.image_id + "' />");
}

function FindIndex(v,t)  
{
	var k=-1 ;

	for (var i=0; i <= v.length-1; i++)  
		if (v[i] == t )  
			{k=i; break;}
	return k ;
}

function favorites(img, id)
{
	if (!favorites && $.cookie('auto_favorites'))
		var favorites = $.cookie('auto_favorites').split(",");
	else
		var favorites = new Array();
		
	var index = FindIndex(favorites, id);
	
	if (favorites.length >= 100)
	{
		$.blockUI({message:more_favorites});
		window.setTimeout($.unblockUI, 1500);
		return false;
	}

	if (favorites.length > 0 && index != -1)
	{
		favorites.splice(index, 1);
		img.src = dle_root + "templates/" + dle_skin + "/car-market/images/plus.gif";
	}
	else
	{
		favorites.push(id);
		img.src = dle_root + "templates/" + dle_skin + "/car-market/images/minus.gif";
	}
		
	$.cookie('auto_favorites', favorites.toString(), {expires: 365, path:"/"});
}

function auto_email_send(auto_id)
{
	id = auto_id;
	
	$.blockUI({message:$("#email_auto")});
	
	return false;
}

function BlockContent(jobj)
{
	width = jobj.width();
	height = jobj.height();
	
	jobj.html("<center style=\"padding:20px;\"><img src=\"" + dle_root + "templates/" + dle_skin + "/car-market/images/loading.gif\" /></center>");
}

function SetSearchModels(models)
{
    var model_id_search = $('#model_id_search');
    $.each(models, function(index, data)
    {
        if (data.id != '' && $.inArray(data.id, SelectedModels) == -1)
         {
            SelectedModels.push(data.id);
            model_id_search.find("option[value=" + data.id + "]").hide();
            if (admin)
            {
                var str = "<span class=\"selected_model\" OnClick=\"removeModel('" + data.id + "');\" id=\"m" + data.id + "\"><br /><span>" + data.name + "<input type='hidden' name='where[model_ids][]' value='" + data.id + "' /> " + "<img src=\"" + dle_root + "engine/car-market/images/admin/minus.gif\" /></span></span>";
            }
            else
            {
                var str = "<span class=\"selected_model\" OnClick=\"removeModel('" + data.id + "');\" id=\"m" + data.id + "\"><br /><span>" + data.name + "<input type='hidden' name='model_ids[]' value='" + data.id + "' /> " + "<img src=\"" + dle_root + "engine/car-market/images/admin/minus.gif\" /></span></span>";
            }
            model_id_search.after(str);
         }
    });
}

function removeModel(id)
{
    $('#model_id_search').find("option[value=" + id + "]").show();
    $("#m" + id).remove();
    var index = $.inArray(id, SelectedModels);
    if (index != -1)
        SelectedModels.splice(index, 1);
}

function SetSearchCities(models)
{
    var city_id_search = $('#city_id_search');
    $.each(models, function(index, data)
    {
        if (data.id != '' && $.inArray(data.id, SelectedCities) == -1)
         {
            SelectedCities.push(data.id);
            city_id_search.find("option[value=" + data.id + "]").hide();
            if (admin)
            {
                var str = "<span class=\"selected_city\" OnClick=\"removeCity('" + data.id + "');\" id=\"c" + data.id + "\"><br /><span>" + data.name + "<input type='hidden' name='where[city_ids][]' value='" + data.id + "' /> " + "<img src=\"" + dle_root + "engine/car-market/images/admin/minus.gif\" /></span></span>";
            }
            else
            {
                var str = "<span class=\"selected_city\" OnClick=\"removeCity('" + data.id + "');\" id=\"c" + data.id + "\"><br /><span>" + data.name + "<input type='hidden' name='city_ids[]' value='" + data.id + "' /> " + "<img src=\"" + dle_root + "engine/car-market/images/admin/minus.gif\" /></span></span>";
            }
            city_id_search.after(str);
         }
    });
}

function removeCity(id)
{
    $('#city_id_search').find("option[value=" + id + "]").show();
    $("#c" + id).remove();
    var index = $.inArray(id, SelectedCities);
    if (index != -1)
        SelectedCities.splice(index, 1);
}

$(document).ready(function()
{
	$('#mark_id').change(function()
	{
		Element = '#model_id';
		GetValues($(this).val(), 'GetModel');
	});
	
	$('#country_id').change(function()
	{
		if (use_region)
		{
			Element = '#region_id';
			$('#city_id').val('0').attr('disabled', 'disabled');
			GetValues($(this).val(), 'GetRegion');
		}
		else
		{
			Element = '#city_id';
			GetValues($(this).val(), 'GetCity');
		}
		return false;
	});
	
	$('#region_id').change(function()
	{
		Element = '#city_id';
		GetValues($(this).val(), 'GetCity');
	});
	
	if ($('#model_id').val() == 0)
		$('#model_id').attr('disabled', 'disabled');
		
	if ($('#region_id').val() == 0 && use_country)
		$('#region_id').attr('disabled', 'disabled');
		
	if ($('#city_id').val() == 0 && (use_country || use_region))
		$('#city_id').attr('disabled', 'disabled');
		
	$('#mark_id_search').change(function()
	{
	    $(".selected_model").remove();
        SelectedModels = new Array();
        
		Element = '#model_id_search';
		GetValues($(this).val(), 'GetModel', true);
	});
	
	$('#model_id_search').change(function()
	        {
        	    if (this.value != 0 && this.value != -1)
                {
        	        SetSearchModels(new Array({id:this.value, name:this.options[this.selectedIndex].text}));
                }
                else
                {
                    $(".selected_model").remove();
                    $(this).find("option").show();
                    
                    SelectedModels = new Array();
                }
	        });
	
	$('#country_id_search').change(function()
	{
	    $(".selected_city").remove();
        SelectedCities = new Array();
	    
		if (use_region)
		{
			Element = '#region_id_search';
			$('#city_id_search').val('0').attr('disabled', 'disabled');
			GetValues($(this).val(), 'GetRegion', true);
		}
		else
		{
			Element = '#city_id_search';
			GetValues($(this).val(), 'GetCity', true);
		}
	});
	
	$('#city_id_search').change(function()
            {
                if (this.value != 0 && this.value != -1)
                {
                    SetSearchCities(new Array({id:this.value, name:this.options[this.selectedIndex].text}));
                }
                else
                {
                    $(".selected_city").remove();
                    $(this).find("option").show();
                    
                    SelectedCities = new Array();
                }
            });
	
	$('#region_id_search').change(function()
	{
	    $(".selected_city").remove();
        SelectedCities = new Array();
        
		Element = '#city_id_search';
		GetValues($(this).val(), 'GetCity', true);
	});
	
	if ($('#model_id_search').val() == 0 && $('#model_id_search > option').length == 1)
		$('#model_id_search').attr('disabled', 'disabled');
		
	if ($('#region_id_search').val() == 0 && $('#region_id_search > option').length == 1)
		$('#region_id_search').attr('disabled', 'disabled');
		
	if ($('#city_id_search').val() == 0 && $('#city_id_search > option').length == 1)
		$('#city_id_search').attr('disabled', 'disabled');
		
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
			$(this).attr("src", dle_root + "templates/" + dle_skin + "/car-market/images/compare_checked.gif");
			$(".compare").each(function()
			{
				$(this).attr("src", dle_root + "templates/" + dle_skin + "/car-market/images/compare_unchecked.gif");
			});
		}
		else
		{
			compareAll = true;
			$(this).attr("src", dle_root + "templates/" + dle_skin + "/car-market/images/compare_unchecked.gif");
			$(".compare").each(function()
			{
				compare.push($(this).attr("id"));
				$(this).attr("src", dle_root + "templates/" + dle_skin + "/car-market/images/compare_checked.gif");
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
	
	$("a.mail_friend").click(function()
	{
		var auto_url = $(this).attr("href");
		$("#send_form textarea").val($("#send_form textarea").val().replace(/http:.*?id=[0-9]+/, auto_url));
		$("#send_form textarea").val($("#send_form textarea").val().replace(/http:.*?auto-[0-9]+.html/, auto_url));
		$("#send_form textarea").val($("#send_form textarea").val().replace(/__auto_link__/, auto_url));
		$.blockUI({message:$("#send_mail")});
	});
	
	$("#send_form input:button").click(function()
	{
		$.unblockUI();
	});
	
	$("#send_form").submit(function()
	{
		var error = false;
		
		if ($("#send_form input[name=user_email]").val().match(/^[\w-]+(\.[\w-]+)*@([\w-]+)\.+[a-zA-Z]{2,3}$/) == null)
		{
			$("#send_form input[name=user_email]").after("<label class='error'>" + email_error + "</label>");
			error = true;
		}
		
		if ($("#send_form input[name=from_email]").length > 0 && $("#send_form input[name=from_email]").val().match(/^[\w-]+(\.[\w-]+)*@([\w-]+)\.+[a-zA-Z]{2,3}$/) == null)
		{
			$("#send_form input[name=from_email]").after("<label class='error'>" + email_error + "</label>");
			error = true;
		}
		
		if ($("#send_form input[name=from_name]").length > 0 && $("#send_form input[name=from_name]").val() == '')
		{
			$("#send_form input[name=from_name]").after("<label class='error'>" + from_name_error + "</label>");
			error = true;
		}

		if ($("#send_form textarea").val().length < 10)
		{
			$("#send_form textarea").before("<label class='error'>" + text_error + "</label>");
			error = true;
		}
		
		if ($("#send_form input[name=subj]").val().length < 5)
		{
			$("#send_form input[name=subj]").after("<label class='error'>" + subj_error + "</label>");
			error = true;
		}
		
		if (error)
			return false;

		$.blockUI({message:"<img src='" + dle_root + "templates/" + dle_skin + "/car-market/images/loading.gif' /> " + send});
		$.post(ajax_url, {'action':'send_mail', 'data':$(this).serialize()}, function(data)
		{
			if (data == 'ok' )
			{
				$.blockUI({message:send_ok});
				window.setTimeout($.unblockUI, 1500);
			}
			else
			{
				$.blockUI({message:send_error});
				window.setTimeout($.unblockUI, 2500);
			}
				
		});
		return false;
	});
	
	$("#auto_send").submit(function()
	{
		var error = false;
		
		if ($("#auto_send input[name='from_email']").length > 0 && $("#auto_send input[name='from_email']").val().match(/^[\w-]+(\.[\w-]+)*@([\w-]+)\.+[a-zA-Z]{2,3}$/) == null)
		{
			$("#auto_send input[name='from_email']").after("<label class='error'>" + email_error + "</label>");
			error = true;
		}
		
		if ($("#auto_send input[name=from_name]").length > 0 && $("#auto_send input[name=from_name]").val() == '')
		{
			$("#auto_send input[name=from_name]").after("<label class='error'>" + from_name_error + "</label>");
			error = true;
		}

		if ($("#auto_send textarea").val().length < 10)
		{
			$("#auto_send textarea").before("<label class='error'>" + text_error + "</label>");
			error = true;
		}
		
		if ($("#auto_send input[name=subj]").val().length < 5)
		{
			$("#auto_send input[name=subj]").after("<label class='error'>" + subj_error + "</label>");
			error = true;
		}
		
		if (error)
			return false;

		$.blockUI({message:"<img src='" + dle_root + "templates/" + dle_skin + "/car-market/images/loading.gif' /> " + send});
		$.post(ajax_url, {'action':'email_auto', 'data':$(this).serialize(), "id":id}, function(data)
		{
			if (data == 'ok' )
			{
				$.blockUI({message:send_ok});
				window.setTimeout($.unblockUI, 1500);
			}
			else
			{
				$.blockUI({message:send_error});
				window.setTimeout($.unblockUI, 2500);
			}
				
		});
		return false;
	});
	
	$("#auto_send input:button").click(function()
	{
		$.unblockUI();
	});
});