function postwith(to, p) {  //{'key1':'value','key2':'value'}
			var myForm = document.createElement("form");  
			myForm.method = "post";  
			myForm.action = to;
			for ( var k in p) {  
				var myInput = document.createElement("input");  
				myInput.setAttribute("name", k);  
				myInput.setAttribute("value", p[k]); 
				myForm.appendChild(myInput);  
			}  
			document.body.appendChild(myForm);  
			myForm.submit();
			document.body.removeChild(myForm);  
		}

function execute(to, p) {  //{'key1':'value','key2':'value'}
	form = $("<form></form>");
	form.attr("method","post");
	form.attr("action",to);
	for ( var k in p) {  
		input = $("<input type='hidden'/>");
		input.attr('name',k);
		input.attr('value',p[k]);
		form.append(input); 
	}
	form.on('submit', function() {
		$(this).ajaxSubmit({
		    success: function(data) { // data 保存提交后返回的数据，一般为 json 数据
		        res = (new Function("return " + data))();// 此处可对 data 作相关处理
		        if(res['Fail']==0) alert('Successful！');
				else alert('Fail to delete!');
				}
		});
		return false; // 阻止表单自动提交事件
	    });
	form.appendTo("body");
	form.css('display','none');
	form.submit();
	form.remove();  
}

function stopsubmit() {
        var title = $('inpur[name=title]').val(),
            content = $('textarea').val();

        $(this).ajaxSubmit({
            success: function(data) { // data 保存提交后返回的数据，一般为 json 数据
                res = (new Function("return " + data))();// 此处可对 data 作相关处理
                if(res['Fail']==0) alert('Successful！');
		else alert('Error!');
            }
        });
        return false; // 阻止表单自动提交事件
}
