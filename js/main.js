var debugMode = false;

function toConsole(message){
	if(debugMode)
		console.log(message);
}

function getFileName(path){
	return path.replace(/^.*[\\\/]/, '');
}
function getParentFolder(path){
	path = path.replace(path.split("/").pop(),"");
	path = path.replace('/','');
	return path;
	
}

function isReportStatusGoodAndInputsFilled(){
	if($('#status_id').val() != ''){
		if($('#status_id').val() > 2) {
			// toConsole('status shoud bould be empty 0 or 1');
			$('#form_messages').show().text('Проверка проведена, менять поля/загружать фото нельзя');
			return false;
		}
    }
	
	for (var i = 0; i < $('.report-input').length; i++) {
		var input = $('.report-input')[i];
		if(input['value'] == ""){
			// toConsole(input['name'] + " is empty go out");
			return false;
		}
	}
	return true;
}

function getFileNamesByReportId(id) {
	return getFilesByReprtId(id).map(function(a) {return a.foto_old_name;});
}

function getFilesByReportId(id) {
	var toReturn = [];
	/*
	 * $.post("report", { id: id, action: 'getFotos' }, function(result){
	 * toReturn = result; }, 'json');
	 */
	$.ajax({
		  method: "POST",
		  dataType: 'json',
		  url: "report",
		  data: { id: id, action: 'getFotos'},
		  async:false
		})
		  .done(function( result ) {
			  toReturn = result;
			  // toConsole(toReturn);
		  });
	
	return toReturn;
}

function fillTableOfLoadedFotos(id){
	// toConsole('I\'m in fillTableOfLoadedFotos start');
	$('#tbody_foto_exists').empty();
	   $.post("report",
			    {
			        id: id,
			        action: "getFotos"
			    },
			    function(data, status){
			    	// toConsole("Data: " + data + "\nStatus: " + status);
			    	$('#tbody_foto_exists').empty();
			    	$('.fotos_count').text(data.length);
			    	if(data.length > 0){
			    		$("#delete_all_fotos").show();
			    	}
			    	$.each(data, function( index, foto ) {
			    		 var row = $('<tr></tr>').attr('id', 'tr' + foto.id);
			    		 var img = $('<img>').attr('src', $('#base_url').text()+'/smallfoto/'+foto.foto_name).addClass("img-responsive img-sizing_report_page img-rounded");
			    		 var td1 = $('<td>').append(img);
			    		 var td2 = $('<td>').append(foto.foto_old_name).addClass('foto_old_names');
			    		 var td3 = $('<td>').text(foto.width + 'x' + foto.height);
			    		 var td4 = $('<td>').attr('id', 'td' + foto.id);
			    		 var aDelete = $('<a>').attr('id', 'link_foto_delete' + foto.id).addClass("label label-danger link_foto_delete").text('Удалить');
			    		 td4.append(aDelete);
			    		 //<a id='link_foto_delete{{@foto.id}}' class='label label-danger link_foto_delete'>
			    		 td1.appendTo(row);
			    		 td2.appendTo(row);
			    		 td3.appendTo(row);
			    		 td4.appendTo(row);
			    		 row.appendTo($('#tbody_foto_exists'));
			    		 
			    		});
			    },
	    		'json'
			    );
	
	// toConsole('End I\'m in fillTableOfLoadedFotos start');
}

function todoFinalActions(){
	var sent_files = $('.processedFiles').length;
	var total_sent_files = $('.liFiles').length;
	$('#fotoLoadingFinishDiv').css('display', '');
	$('#alertWaitFileLoading').css('display', 'none');
	
	var timeText = "";
	var timeInSec = (Date.now() - $('#startUpload').text())/1000;
	if(timeInSec > 60){
		timeText = Math.round((timeInSec/60)) + " мин. " + Math.round(timeInSec/60) + " сек."
	}else{
		timeText = Math.round(timeInSec) + " сек."
	}
	
	
	$('#statistica_loaded').text("Всего: " + total_sent_files + ", c ошибкой: " + $('.errorProcessed').length + '. Время: '+timeText);
	if($('.errorProcessed').length == 0){
		$('#statistica_loaded').attr('class', 'label label-success');
	}else{
		$('#statistica_loaded').attr('class', 'label label-warning');
	}
	$('#progress_active_id').css('width', (sent_files*100/total_sent_files) +'%');
    $('#progress_active_id').text("Обработано " + sent_files + "/" + total_sent_files + " файлов");
}

function resizeAndUpload(file, index, totalFiles) {
	toConsole('Start resizeAndUpload');
	
	var reader = new FileReader();
    reader.fileName = file.name;
	reader.onerror = errorHandler;
	
	reader.onloadstart = function(evt) {
		$('#progress_each_file_id').css('width', '0%');
		$('#progress_each_file_id').text('');
	}
	reader.onprogress = function(evt) {
		 if (evt.lengthComputable) {
		  var percentLoaded = Math.round((evt.loaded / evt.total) * 100);
		  // Increase the progress bar length.
		  if (percentLoaded < 100) {
			$('#progress_each_file_id').css('width', percentLoaded + '%');
			$('#progress_each_file_id').text('Файл ' + this.fileName + ' ' + percentLoaded + '%');
          }
         }
	}
	
	reader.onloadend = function() {
		$('#progress_each_file_id').css('width', '100%');
		$('#progress_each_file_id').text('Файл ' + this.fileName + ' считан полностью');


		var tempImg = new Image();
		tempImg.src = reader.result;
		tempImg.onload = function() {

			var MAX_WIDTH_OR_HEIGHT = $("#max_img_width").text() != '' ? $("#max_img_width").text() : 800;
			var tempW = tempImg.width;
			var tempH = tempImg.height;
			
			if((tempW > MAX_WIDTH_OR_HEIGHT) || (tempH > MAX_WIDTH_OR_HEIGHT)) {
				var coef = tempW > tempH ? tempW/tempH :  tempH/tempW;
				if (tempW > tempH) {
					tempW = MAX_WIDTH_OR_HEIGHT;
					tempH = tempW / coef;
				}else{
					tempH = MAX_WIDTH_OR_HEIGHT;
					tempW = tempH / coef;
				}
			}

			var canvas = document.createElement('canvas');
			canvas.width = tempW;
			canvas.height = tempH;
			var ctx = canvas.getContext("2d");
			ctx.drawImage(this, 0, 0, tempW, tempH);
			var dataURL = canvas.toDataURL("image/jpeg");

			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function(ev) {
				// document.getElementById('filesInfo').innerHTML = 'Done!';
				if (xhr.readyState == 4) {
					if (xhr.status == 200){
					// alert("Done!");
						toConsole(xhr.responseText);
						json = JSON.parse(xhr.responseText);
						var elFoto = document.getElementById(json.fileName);
						
						var labelspan = document.getElementById('label_foto_'+json.fileName);
						
						if (json.error){
							elFoto.textContent = "Ошибка: " + json.message;
							elFoto.className = "label label-warning";
							labelspan.className = "label label-warning liFiles processedFiles errorProcessed";
						} else {
							elFoto.textContent = "Загружен";
							elFoto.className = "label label-success";
							labelspan.className = "label label-success liFiles processedFiles";
						}
						
						var sent_files = $('.processedFiles').length;
						var total_sent_files = $('.liFiles').length;
						
						if(sent_files == total_sent_files) {
							fillTableOfLoadedFotos($('#id').val());
							todoFinalActions();
							
						}else{
						
						   $('#progress_active_id').css('width', (sent_files*100/total_sent_files) +'%');
					       $('#progress_active_id').text("" + sent_files + "/" + total_sent_files + " файлов обработано");
						}
				   } else {
					   toConsole(xhr.responseText);
					   $('#form_messages').show();
					   $('#form_messages').append('<br>Ошибка при передаче файла: ' 
							   + xhr.status + ", " + xhr.responseText + '</br>');
				   }

					
				}
			};
			
			xhr.open('POST', 'report', true);
			// xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			// var data = 'image=' + dataURL;
			
			var fd = new FormData();
			fd.append("sendFiles", "1");
			fd.append("image", dataURL);
			fd.append("id", $('#id').val());
			fd.append("file_name", file.name);
			fd.append("file_index", index);
			fd.append("file_total", totalFiles);
			xhr.send(fd);
		}

	}
	reader.readAsDataURL(file);
	toConsole('Finish resizeAndUpload');
}

function errorHandler(evt) {
  switch(evt.target.error.code) {
      case evt.target.error.NOT_FOUND_ERR:
        $('#form_messages').show().text('Файл ' + this.fileName + ' не найден!');
        break;
      case evt.target.error.NOT_READABLE_ERR:
        $('#form_messages').show().text('Файл ' + this.fileName + ' не читабельный');
        break;
      case evt.target.error.ABORT_ERR:
        break; // noop
      default:
        $('#form_messages').show().text('Ошибка при чтении файла ' + this.fileName + ' .');
   }
}

$(document).ready(function () {
	// var try_sent_emails = 0;
	// var sent_emails = 0;
	
	
	$('#fotoLoadingFinish').on('click',function(e){
		// str.endsWith("universe.");
		
		$('#form_messages').hide().text('');
		if(!isReportStatusGoodAndInputsFilled()){
			e.preventDefault();
			$('#form_messages').show().text('Не все поля заполнены');
			return;
		}
		
		var myHref = "";
		if (window.location.href.endsWith("/report")) {
		      // if(window.location.hostname == "www.myweb.com"){
			myHref = window.location.href + "/" + $('#id').val();
		}else if (window.location.href.endsWith("/new")) {
			myHref = window.location.href.replace("/new", "/" + $('#id').val());
		}else if (window.location.href.endsWith("/fotoLoad")) {
			myHref = window.location.href.replace("/fotoLoad", "");
		}else{
			myHref = window.location.href;
		}
		window.location.href = myHref + "/fotoLoad";
	return false;
    });
	
	$('#loadfotolink').on('click',function(e){
		e.preventDefault();
		try{
			$('#form_messages').hide().text('');
			if(!isReportStatusGoodAndInputsFilled()){
				$('#form_messages').show().text('Не все поля заполнены');
				return;
			}
			$('#fileInputId').click();
		}catch(e){
			alert(e.message);
			toConsole(e);
		}
		return false;
	});
	
	$('#fileInputId').on('change',function(e){
		try{
			toConsole($(this).prop('files').length);
			var files = $(this).prop('files');
			if (!files.length) {
				fileList.innerHTML = "<p>Файлы не выбраны!</p>";
			} else {
				
				$('#progress_each_file_div').show();
				$('#progress_active_div').show();
				
				$('#startUpload').text(Date.now());
				$('#fotoLoadingFinishDiv').css('display', 'none');
				$('#alertWaitFileLoading').css('display', '');
				$('#form_messages').hide();
				
				$('#statistica_loaded').text('');
				
				fileList.innerHTML = "";
				var list = document.createElement("ul");
				list.className = "list-inline";
				fileList.appendChild(list);
				
				sent_files = 0;
				total_sent_files = files.length;
				sent_files_step = 100 / files.length;
				
				$('#progress_active_id').text('Началась отправка, ждите...');
				$('#progress_active_id').width("0%");
				
				while(fileLabels.firstChild) {
					fileLabels.removeChild(fileLabels.firstChild);
				}
				
				
				for (var i = 0; i < files.length; i++) {
					var span = document.createElement("li");
					span.id = "label_foto_" + files[i].name;
					span.className = "label label-default liFiles";
					// span.textContent = i + '. ' + files[i].name;
					span.textContent = i+1;
					fileLabels.appendChild(span);
				}
				
				for (var i = 0; i < files.length; i++) {
					var li = document.createElement("li");
					li.className = "list-group-item";
					list.appendChild(li);
					
					var fileName = document.createElement("span");
					fileName.className = "label label-default";
					fileName.innerHTML = files[i].name;
					li.appendChild(fileName);
					li.appendChild(document.createElement("br"));

					var img = null;
					var toShowImages = false;
					if(toShowImages){ //убрал показ картинок, много памяти кушает
						var img = document.createElement("img");
						// img.className = "obj";
						img.className = "img-rounded";
						img.src = window.URL.createObjectURL(files[i]);
						img.height = 60;
						img.onload = function() {
							window.URL.revokeObjectURL(this.src);
						}
						li.appendChild(img);
					}else{ // to show labels instead images
						//var img = document.createElement("span");
						// img.className = "obj";
						//img.className = "label label-default";
						//img.innerHTML = files[i].name ;
					}
					if(img != null){
					  li.appendChild(img);
					  li.appendChild(document.createElement("br"));
				    }
					var info = document.createElement("span");
					info.className = "label label-default";
					info.id = files[i].name;
					// info.innerHTML = files[i].name + ": " + files[i].size + "
					// bytes";
					li.appendChild(info);
					

				}

				var foto_old_names = $('.foto_old_names').map(function(){
		               return $.trim($(this).text());
		            }).get();
				
				for (var i = 0; i < files.length; i++) {
					if($.inArray(files[i].name, foto_old_names) > -1){
						var elFoto = document.getElementById(files[i].name);
						elFoto.textContent = "Ошибка: файл уже существует";
						elFoto.className = "label label-warning";
						var labelspan = document.getElementById('label_foto_' + files[i].name);
						labelspan.className = "label label-warning liFiles processedFiles errorProcessed";
						if(i == files.length-1) {
							todoFinalActions();
						}
						continue;
					}
					resizeAndUpload(files[i], i, files.length);
				}
			}
			
			
			
		}catch(e){
			console.log(e.stack);
			alert(e.message);
		}
		return false;
	});
	
	
	
	$('.report-input').on('change', function(e){
		toConsole("Start #report-input change");
		
		if(!isReportStatusGoodAndInputsFilled()){
			return;
		}
		
		var allFieldsOfForm = $('#myReportForm input, #myReportForm select');
		var formData = new FormData();
		for (var i = 0; i < allFieldsOfForm.length; i++) {
			var input = allFieldsOfForm[i];
			if(input['type'] == 'file' || input['type'] == 'submit'){
	        	continue;
	        }
	        formData.append(input['name'], input['value']);
		}
		
		toConsole("go to ajax");
		if(1==0) {
			toConsole("go out tempo");
			return;
		}
		$.ajax({
            url: 'report', // point to server-side PHP script
            dataType: 'json',  // what to expect back from the PHP script, if
								// anything
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            type: 'post',
            success: function(resp){
            	toConsole(resp); // display response from the PHP script, if
									// any
                if(resp.error){
                	toConsole("ajax error"); // display response from the PHP
												// script, if any
                }
                else{
                    // var success = $('<div class="alert
					// alert-success">Фотографии добавлены</div>');
                	$('#id').val(resp.id);
                	$('#status_id').val(resp.status_id);
                	
                	$('#report_id').text(resp.id);
                	if(resp.status_id == 0) {
                		$('#report_status_id').text('Присовоен номер');
                	}else if (resp.status_id == 1) {
                		$('#report_status_id').text('Загружен');
					}
                	
                	$('#fotoUploadDiv').css('display', '');
                    toConsole("ajax success"); // display response from the PHP
												// script, if any
                }
            }
        });
		toConsole("Finish #report-input change");
	});
	
	$('.mark_as_send').on('click', function(e){
		$('#progress_active_div').css('display', 'none');
		if(!confirm("Отметить отправленным?")){
			return;
		}
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'send_email',
			data: {
				send_email: 1,
				mark_as_send: 1,
				category_id: $(this).attr('id').replace('check_emailsend_','')
            },
            success: function(result){
            	toConsole(result);
            	var result_span = $("#result_email_"+result.category_id);
            	if(result.error == '') {
                	result_span.attr('class', 'label label-success');
                	result_span.text("OK (вручную)");
                	$("#check_emailsend_"+result.category_id).css('display','none');
                	if(result.all_sent){
                		$("#report_status_id").text('Отправлен');
                	}
                }else{
                	result_span.attr('class', 'label label-danger');
                	result_span.text("Ошибка: " + result.error);
                	$("#check_emailsend_"+result.category_id).css('display','');
                }
            }
	   });
	});
		
	
		
	
	$('#send_mail_button').on('click', function(e){
		$('#progress_active_div').css('display', 'none');
		$("#email_error_div").hide().text('');
		if(!confirm("Отправить отчеты по эл.почте?")){
			return;
		}
		// var url = '{{@BASE}}/calculate/send_email';
		var url = 'send_email';
		var trCutegories = $(".tr_category");
		$('#progress_active_div').css('display', '');
		// $('#progress_active_id').css('width', '0%');
		$('#progress_active_id').css('width', '30%');
		$('#progress_active_id').attr('class', 'progress-bar progress-bar-info');
		sent_emails = 0;
		try_sent_emails = 0;
		for(var i=0; i < trCutegories.length; i++){
			
			if(i == 0) {
				$('#progress_active_id').text('Началась отправка, ждите...');
			}
			var category = trCutegories[i];
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: url,
				data: {
					send_email: 1,
					category_id: category.id.replace('categoryid_',''),
					report_id: $('#report_id').text().trim()
	            },
	            success: function(result){
	                // $("#div1").html(result.name);
	                $("#div1").html(JSON.stringify(result));
	                toConsole(result);
	                try_sent_emails++;
	                
	               // $('#progress_complete_id').css('display', '');
	               // $('#progress_active_id').css('display', 'none');
	                var all_emails = $(".tr_category").length;
	                var result_span = $("#result_email_"+result.category_id);
	                if(result.error == '') {
	                	sent_emails++;
	                	result_span.attr('class', 'label label-success');
	                	result_span.text("OK");
	                	$("#check_emailsend_"+result.category_id).css('display','none');
	                	if(result.all_sent){
	                		$("#report_status_id").text('Отправлен');
	                	}
	                }else{
	                	result_span.attr('class', 'label label-danger');
	                	result_span.text("Ошибка: " + result.error);
	                	$("#check_emailsend_"+result.category_id).css('display','');
	                }
	                
	                if(try_sent_emails < all_emails){
	                	var procent_done = sent_emails * 100 / all_emails;
	                	$('#progress_active_id').css('width', procent_done +'%');
	                	// if()
	                	$('#progress_active_id').text('Обработано ' + (sent_emails) + ' из ' + all_emails);
	                }else{
	                	if(sent_emails == all_emails){
	                		$('#progress_active_id').addClass('progress-bar-success').removeClass('progress-bar-info');
	                	}else{
	                		$('#progress_active_id').addClass('progress-bar-warning').removeClass('progress-bar-info');
	                	}
	                	$('#progress_active_id').css('width', '100%');
	                	$('#progress_active_id').text('Отправка завершена. Успешно отправленных  ' + (sent_emails) + ' из ' + all_emails);
	                }
	                
	            },
	            error: function (jqXHR, status, error) {
	            	$("#email_error_div").show();
	            	$("#email_error_div").append("Ошибка (что-то пошло не так): " + error +"<br/>");
	            }
	           });
		}
		return false;
	});
	$('#add_category_row').on('click', function(e){
		  var $div = $('div[id^="example_cat_div"]:last');

		  var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
          var nextId = 'example_cat_div' + num;
		  var $div_clone = $div.clone(true).prop('id', nextId);
		  
		  $div_clone.appendTo('#panel_container');
		  
		  $div_clone.find(":input").map(function (index,item) {
			  $(item).val("");
		  });
		  return false;
	});
	// $(this).
	$('.removecat').on('click', function(e){
		if($('.panel_cat').length == 1) {
			return false;
		}
		$(this).parent().parent().remove();
		return false;
	});
	
	/**
	 * @deprecated
	 */
	$('#myForm111').submit(function (e) {
        e.preventDefault();
        numUploded = 0;
        var files = $('#image')[0].files;
        var formData = new FormData();
        // var obj = {};
        $(this).each(function () {
            $(this).find(':input').map(function (index,item) {
                if(($(item).attr('name') !== 'fileToUpload') && $(item).attr('type') !== 'submit'){
                    // obj[$(item).attr('name')] = $(item).val();
                    formData.append($(item).attr('name'), $(item).val());
                }
            });
        });
        var url = 'functions/fileUpload.php';
        $.ajax({
            url: url, // point to server-side PHP script
            dataType: 'json',  // what to expect back from the PHP script, if
								// anything
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            type: 'post',
            success: function(resp){
            	toConsole(resp);
            	if(1==0){
                if(resp.error){
                    var error = $('<div class="alert alert-danger">'+resp.error+'</div>');
                    $('#imgSections').append(error);
                }
                else{
                	var success = $('<div class="alert alert-success">Фотографии добавлены</div>');
                    $('#imgSections').append(success);
                    $('#images-load').attr('data-id', resp.resp[0][1]);
                    $('.nav-tabs').show();
                }
                }
            }
        });

        return false;
    });
	
	$('.foto_match_checkbox').on('change', function(e){
		$brothers = $("input[name='"+ $(this)[0].name +"']");
		if($(this)[0].checked){
			if($(this).val() == 'address' || $(this).val() == 'delete'){
				for(var i = 0; i < $brothers.length; i++){
					if($brothers[i].value == $(this).val()){
						continue;
					}
					$brothers[i].checked=false;
				}
			}else{
				for(var i = 0; i < $brothers.length; i++){
					if($brothers[i].value == 'address' || $brothers[i].value == 'delete'){
						$brothers[i].checked=false;
					}
				}
			}
		}
		// var
		var labelInListId = 'labelInList' + $(this)[0].parentElement.parentElement.parentElement.id.replace('radiosDiv','');
		$("#"+labelInListId).text('');
		for(var i = 0; i < $brothers.length; i++){
			if($brothers[i].checked){
				$ul = $("#"+labelInListId);
				
				$radioText = $('#'+$brothers[i].id.replace("radio", "radioText"));
				$liNew = $("<li></li").text($radioText.text());
				$liNew.addClass('list-group-item');
				// list-group-item list-group-item-default
				// $spanNew = $("<span></span").text($radioText.text());
				// $spanNew.addClass('label');
				if($brothers[i].value == 'address'){
					$liNew.addClass('disabled');
				}else if ($brothers[i].value == 'delete') {
					$liNew.addClass('list-group-item-danger');
				}else{
					$liNew.addClass('list-group-item-info');
				}
				
				$liNew.append($liNew);
				
				$ul.append($liNew);
				
				
			}
		}
		
		
	});
	
	$("#save_category_match_btn_on_modal").click(function(event){
		event.preventDefault();
		$('#save_category_match_btn').click();
	});
	
	// save_category_match_btn
	$('#save_category_match_btn').on('click', function(e){
		// if(1==1) return;
		
		// Первое фото (есле не удаленное) должно быть адресом
		/*
		 * for(var i=0; i < $('input:checked').length; i++){ var radioInput =
		 * $('input:checked')[i]; if(radioInput['value'] == 'delete') continue;
		 * if(radioInput['value'] == 'address') break; else { alert ('Первое
		 * фото (есле не удаленное) должно быть адресом'); return false; } }
		 */
		
		// хоть одно должно быть адресом
		/*
		 * if ($('input:checked[value=address]').length == 0) { alert ('Как
		 * миниму 1 фото должно быть адресом'); return false; }
		 */
		// Два подряд адреса не должно быть
		/*
		 * for(var i=1; i < $('input:checked').length; i++){ var radioInput =
		 * $('input:checked')[i]; var prevIadioInput = $('input:checked')[i-1];
		 * if(radioInput['value'] == 'address' && prevIadioInput['value'] ==
		 * 'address'){ alert ('Два адреса подряд не должно быть'); return false; } }
		 */
		// Последнее фото не должно быть адресом
		/*
		 * if ($('input:checked')[$('input:checked').length-1]['value'] ==
		 * 'address') { alert ('Последнее фото не должно быть адресом'); return
		 * false; }
		 */
		
		// $("input[type='checkbox'")[0].name
		for(var i=1; i < $("input[type='checkbox'").length; i++){
			var name = $("input[type='checkbox'")[i].name;
			if($("input:checked[name='" + name + "'").length < 1){
				alert('Не все фото отмечены!');
				return false;
			}
		}
		
		$('#save_category_match_btn_run').click();
		
	});
	
	
	$(".map_load_link").click(function(event){
		event.preventDefault();
	    var butId = event.target.id.replace('link_map_load','but_map_load');
	    //toConsole(butId);
	    $('#' + butId).click();
	});
	
	$(".map_load_input").change(function(event){
		//toConsole($(this).files.length);
		if($(this).prop('files').length==1){
			var reader = new FileReader();
			reader.region_id=event.target.id.replace('but_map_load','');
			var file = $(this).prop('files')[0];
			reader.onload = function(event) {
				//object.filename = file.name;
			    //object.data = event.target.result;
			    //files.push(object);
				var formData = new FormData();
				
				var fd = new FormData();
				formData.append('id', this.region_id);
				formData.append("action", 'upload');
				formData.append('fileToUpload', file, file.name);
				formData.append('entity', 'region');
				
			    $.ajax({
			    	url: 'dictionary/region', // point to server-side PHP script
                    dataType: 'json',  // what to expect back from the PHP script, if anything
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    type: 'post',
                    success: function(resp){
                    	console.log(resp);
                    	if(resp.error){
                    		//alert(resp.message);
                            var error = $('<div class="alert alert-danger">'+resp.message+'</div>');
                            $('#td' + resp.id).text('').append(img);
                        } else {
                        	//<img width='100' src='{{@BASE}}/map_files/{{@item.file_name}}' class="img-rounded" alt="Карта района {{@item.name}}"/>
                        	var src_parth = $('#base_url').text() + '/map_files/' + resp.file_name;
                        	var img = $("<img width='100' class='img-rounded' src='" + src_parth + "'>");
                        	
                        	var delete_label = $("<a id='link_map_delete" + resp.id + "' class='label label-danger'>Удалить файл</a>");
                        	delete_label.addClass('link_map_delete');
                        	
                        	$('#td' + resp.id).empty();
                        	//$('#td' + resp.id).text('');
                        	$('#td' + resp.id).append(img).append(delete_label); 
                        }
                    },
    	            error: function (jqXHR, status, error) {
    	            	var error = $('<div class="alert alert-danger">'+error+'</div>');
    	            	$("#table_dic_message").append(error);
    	            }
			    });  
			  };  
			  reader.readAsDataURL(file);
		}
	});
	
	$(document).on('click', 'a.link_map_delete', function (event) {
	//$("a").on("click", ".link_map_delete", function(event){
	//$(".link_map_delete").click(function(event){
		event.preventDefault();
		if(!confirm("Удалить файл?"))
			return;
	    var region_id = event.target.id.replace('link_map_delete','');
	    //toConsole(butId);
	    $.post("dictionary/region", {entity: 'region', id: region_id, action : 'delete' }, function(resp){
	    	toConsole(resp);
	    	var msg = '';
	    	if(resp.error){
	    		msg = $('<div class="label label-warning">Ошибка: '+resp.message+'</div>');
	    	}else{
	    		msg = $('<div class="label label-info">Удален</div>');
	    		$('#td' + resp.id).empty();
	        }
	    	
    		$('#td' + resp.id).append(msg);
	    },'json');
	});
	
	$(document).on('click', 'a.link_foto_delete', function (event) {
		//$("a").on("click", ".link_map_delete", function(event){
		//$(".link_map_delete").click(function(event){
			event.preventDefault();
			if(!confirm("Удалить файл?"))
				return;
		    var foto_id = event.target.id.replace('link_foto_delete','');
		    //toConsole(butId);
		    $.post("report", {foto_id: foto_id, action : 'delete' }, function(resp){
		    	toConsole(resp);
		    	var msg = '';
		    	if(resp.error){
		    		msg = $('<br/><div class="label label-warning">Ошибка: '+resp.message+'</div>');
		    		$('#td' + resp.id).append(msg);
		    	}else{
		    		msg = $('<div class="label label-info">Удален</div>');
		    		//$('#tr' + resp.id).empty();
		    		$('#tr' + resp.id).fadeOut('slow');
		        }
		    },'json');
		});
	
	$("#delete_all_fotos").click(function(event){
		event.preventDefault();
		if(!confirm("Удалить все фото?"))
			return;
		 $.post("report", {id: $('#id').val(), action : 'deleteAllFotos' }, function(resp){
		    	toConsole(resp);
		    	var msg = '';
		    	if(resp.error){
		    		msg = $('<br/><div class="label label-warning">Ошибка: '+resp.message+'</div>');
		    	}else{
		    		msg = $('<div class="label label-success">'+resp.message+'</div>');
		    		$('#delete_all_fotos').hide();
		        }
		    	$('#panel_head_loaded_msg').empty().append(msg);
		    	fillTableOfLoadedFotos($('#id').val());
		    },'json');
	});
	//deleteReport
	$(".deleteReport").click(function(event){
		if(!confirm("Удалить отчёт?"))
			return false;
	});
});