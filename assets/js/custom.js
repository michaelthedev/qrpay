// +------------------------------------------------------------------------+
// | @author        : Michael Arawole (Logad Networks)
// | @author_url    : https://logad.net
// | @author_email  : logadscripts@gmail.com
// | @date          : 16 Mar, 2022 12:54PM
// +------------------------------------------------------------------------+

// Create an instance of Notyf
var notyf = new Notyf({
    position:{x:'right', y:'top'},
    duration:6000,
    dismissible:true,
    types: [
        {
            type: 'info',
            background: '#17a2b8',
            icon: {
                className: 'ri ri-information-line text-white',
                tagName: 'i',
                text: ''
            }
        }
    ]
});
notyf.info = function (message) {
    notyf.open({
        type: 'info',
        message: message
    });
}

// Load User transactions 
function loadTransactions() {
    var QrPayUid = localStorage.getItem('QrPayUid');
    if (localStorage.getItem('QrPayUid') === null) return false;
    
    // Get user data
    xhrRequest({
        url : "api/user-transactions",
        data : {
            "QrPayUid" : QrPayUid
        },
        done : function(data) {
            if (data.error === true) {
                page_error(data.message);
            } else {
                TransactionDataToHTML(data.data);
            }
        }
    })
}

// Convet transaction json data to html
function TransactionDataToHTML(transactions) {
    var arrayLength = transactions.length;
    var transactionHTML = "";
    for (var i = 0; i < arrayLength; i++) {
        var transaction = transactions[i];
        var sign = "+";
        var textClass = 'text-success';
        if (transaction.transType == 'debit') {
            sign = "-";
            textClass = "text-danger";
        }
        var template = '<li class="d-flex mb-4 pb-1">'+
            '<div class="avatar flex-shrink-0 me-3">'+
                '<img src="assets/img/icons/unicons/wallet.png" alt="User" class="rounded" />'+
            '</div>'+
            '<div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">'+
                '<div class="me-2">'+
                    '<small class="text-muted d-block mb-1">'+ transaction.typeText +' ('+ transaction.dateFormatted +' '+ transaction.timeFormatted +')</small>'+
                    '<small class="mb-0">'+ transaction.description +'</small>'+
                '</div>'+
                '<div class="user-progress d-flex align-items-center gap-1">'+
                    '<h6 class="mb-0 ' + textClass +'">'+ sign + ' ' +transaction.amountFormatted +'</h6>'+
                '</div>'+
            '</div>'+
        '</li>';
        transactionHTML += template;
    }
    $("#TransactionsList").html(transactionHTML);
}

// Replace `{{user.name}}...` to actual user data
function populateUserData() {
    var QrPayUid = localStorage.getItem('QrPayUid');
    if (localStorage.getItem('QrPayUid') === null) return false;
    
    // Get user data
    xhrRequest({
        url : "api/user-data",
        data : {
            "QrPayUid" : QrPayUid
        },
        done : function(data) {
            if (data.error === true) {
                page_error(data.message);
            } else {
                var user_data = {
                    user : data.data
                }
                // LOL :D //
                $("[lg-var]").each(function(index, el) {
                    var val_key = $(this).attr('lg-var');
                    var split = val_key.split(".");
                    $(this).text(user_data[split[0]][split[1]]);
                });
                generateQrCode();
            }
        }
    })
}

function generateQrCode(data = null) {
    var QrPayUid = localStorage.getItem('QrPayUid');
    var qrcode = new QRCode(document.getElementById("userQrPayQrcode"), {
        text: '{"QrPayUid":"'+QrPayUid+'"}',
        width: 200,
        height: 200,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
}

// check for userid in localstorage
function checkLogin() {
    /* Local Login method is only for demo purposes. DO NOT use on production server */
    if (localStorage.getItem('QrPayUid') === null) {
        $("#loginModal").modal('show');
        $("div.layout-content-navbar.layout-without-menu").addClass('blur')
    } else {
        $("#loginModal").modal('hide');
        $("div.layout-content-navbar.layout-without-menu").removeClass('blur');
        populateUserData();
        // Set interval to update user balance and transactions count every 30 seconds
        setInterval(populateUserData, 30000);
    }
}

$("[page_id]").click(function(event) {
	event.preventDefault();
	var pageID = $(this).attr('page_id');
	if (pageID == 'paymentHistory') {
		loadTransactions();
	}
	// Open Page modal
	if ($("#"+pageID+"Modal").length) {
		$("#"+pageID+"Modal").modal('show');
	}
});

// Start QR code scanner
function startQRScanner() {
	scanner.start();
	$("#qr-video-container").slideDown();
	$("#captureQR").hide();
	$("#stopCaptureQR").show();
}
// Stop scanner
function stopQRScanner() {
	scanner.stop();
	$("#qr-video-container").slideUp();
	$("#stopCaptureQR").hide();
	$("#captureQR").show();
}

$('#sendMoneyModal').on('hidden.bs.modal', function () {
    stopQRScanner();
});

$('input[type=radio][name=sendMethod]').change(function() {
    $("div.form-check").removeClass('checked');
    $(this).closest("div.form-check").addClass('checked');
    if (this.value == 'userid') {
        $(".qrMethodDiv").slideUp();
        $(".useridMethodDiv").slideDown();
        $("#qrDecoded").attr('disabled', true);
        $("#recipientID").removeAttr('disabled');
        stopQRScanner();
    }
    else if (this.value == 'qr') {
        $(".useridMethodDiv").slideUp();
        $(".qrMethodDiv").slideDown();
        $("#recipientID").attr('disabled', true);
        $("#qrDecoded").removeAttr('disabled');
    }
});

// XHR requests
function ajax_request(url, data, form = true, post = true) {
    if(form == false){
    	var type = "POST";
    	if (post == false) {
    		type = "GET";
    	}
        var send = $.ajax({
            url: url,
            type: type,
            data: data,
            dataType: "json",
            cache: false,
            error: function (xhr) {
                if (xhr.status == 404 || xhr.status == 500) {
                    page_error("An unexpected error seems to have occurred. Now that we know, we're working to fix it ☺. ERROR : "+xhr.status);
                }
            },
        });
    }
    if(form == true){
        var send = $.ajax({
            url: url,
            type: "POST",
            data: data,
            dataType: "json",
            cache: false,
            contentType: false,
            processData: false,
            error: function (xhr) {
                if (xhr.status == 404 || xhr.status == 500) {
                    page_error("An unexpected error seems to have occurred. Now that we know, we're working to fix it ☺. ERROR : "+xhr.status);
                }
            },
        });
    }
    return send;
}

// Handle form submissions
function handleForm (form, options) {
	var btn = form.find('[type=submit]');
	var btn_text = btn.text();
	btn.text('please wait..');
	btn.addClass('disabled');
	btn.attr('disabled', true);
	var response = '';
	var formData = new FormData(form[0]);
	if (options.append) {
		for (var appendKey in options.append) {
			var value = options.append[appendKey];
			formData.append(appendKey, value);
		}
	}
	var req = ajax_request(options.url,formData);
	req.done(function (data) {
		if (data.error == false) {
			page_success(data.message);
			if (options.callback_function) {
				options.callback_function(data);
			}
			btn.text(btn_text);
			btn.removeClass("disabled");
			btn.removeAttr("disabled");
			response = "success";
		} else {
			page_error(data.message);
			btn.text(btn_text);
			btn.removeClass("disabled");
			btn.removeAttr("disabled");
			response = "failed";
		}
	});
	req.fail(function (xhr) {
		response = 'error';
		btn.text(btn_text);
		btn.removeClass("disabled");
		btn.removeAttr("disabled");
	});

	return response;
}

// Custom Submit function
(function($){
	$.fn.ajaxSubmit = function(options) {
		$(this).submit(function(event) {
			event.preventDefault();
			if (options.summernote) {
				options.content = $('#summernote').summernote('code');
			}
			handleForm($(this), options);
		});
      	return this;
   	}; 
})(jQuery);

// Custom ajax request
function xhrRequest(options) {
    // GET or POST request
	GoP = true;
	if (options.get) {
		GoP = false;
	}
	if (!options.form) {
		form = false;
	} else {
		form = true;
	}
	var req = ajax_request(options.url, options.data, form, GoP);
	req.done(function(data) {
		if (options.done) {
			options.done(data);
		}
	});
	req.fail(function(xhr) {
		if (!options.fail || options.fail == 'default') {
			page_error("Xhr request failed");
			console.log(xhr);
		} else {
			options.fail();
		}
	});
}

// Handle Page errors
function page_error(msg = "Some error occurred") {
    notyf.error(msg);
}

// Handle Page success
function page_success(msg) {
    notyf.success(msg);
}