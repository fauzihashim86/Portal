// console.log(Notification.permission);
// if(Notification.permission === "default") {
// 	Notification.requestPermission().then(function(result) {
// 	  console.log(result);
// 	});
// }
// Notification.requestPermission().then(function(status) {
// 	if (status === 'denied') {
// 	  Notification.requestPermission();
// 		console.log(status);
// 	}
// });
$.fn.extend({
	treed: function (o) {
		var openedClass = 'fa-minus';
		var closedClass = 'fa-plus';
		if (typeof o != 'undefined'){
			if (typeof o.openedClass != 'undefined'){
			openedClass = o.openedClass;
			}
			if (typeof o.closedClass != 'undefined'){
			closedClass = o.closedClass;
			}
		};

		var tree = $(this);
		tree.addClass("tree");
		tree.find('li').has("ul").each(function () {
			var branch = $(this); //li with children ul
			branch.prepend("<i class='fa " + closedClass + "'></i>");
			branch.addClass('branch');
			branch.on('click', function (e) {
				if (this == e.target) {
					var icon = $(this).children('i:first');
					icon.toggleClass(openedClass + " " + closedClass);
					$(this).children().children().toggle();
				}
			})
			branch.children().children().toggle();
		});

		tree.find('.branch .indicator').each(function(){
			$(this).on('click', function () {
				$(this).closest('li').click();
			});
		});

		tree.find('.branch>a').each(function () {
			$(this).on('click', function (e) {
				$(this).closest('li').click();
				e.preventDefault();
			});
		});

		tree.find('.branch>button').each(function () {
			$(this).on('click', function (e) {
				$(this).closest('li').click();
				e.preventDefault();
			});
		});
	}
});
function printDiv(elem) {
	$("#"+elem).print();
}
function showClock() {
	getthedate();
	setInterval("showClock()",1000);
}
function filter() {
	level = $('#level').val();
	department = $('#department').val();
	submit = false;
	if(level != "" || department != ""){
		if(level.length == 0) {
			var level = [null];
		}
		if(department.length == 0) {
			var department = [null];
		}
		submit = true;
	}
	if(submit == true){
		$.ajax({
		type: "POST",
		 url: "config/function.php",
		 data: {
			 func: "filter",
			 level: level,
			 department: department
		 },
		 success: function(data) {
			 $("#tblFilter").html(data);
		 }
	 });
	}
}
//login function
function auth() {
	user = $("#user").val();
	pass = $("#pass").val();
	$("#login").prop('disabled', true);
	$("#login").html("<i class='fa fa-gear fa-spin'></i> Loading");
	$.ajax({
	type: "POST",
	 url: "login-auth.php",
	 data: {
		 func: "auth",
		 user: user,
		 pass: pass
	 },
	 success: function(data) {
		 if(data == "false") {
			 fail("Please complete all field!");
		 } else if(data == "wrong") {
			 fail("Invalid User ID or Password");
		 } else if(data == "db") {
			 fail("Please Contact System Administrator");
		 } else {
			 window.location="./?id=1";
		 }
		 $("#login").prop('disabled', false);
		 $("#login").html("Login");
	 }
 });
}
function log() {
	email = $("#email").val();
	$("#login").prop('disabled', true);
	$("#login").html("<i class='fa fa-gear fa-spin'></i> Loading");
	if(!email) {
		fail("Please complete all field!");
		$("#login").prop('disabled', false);
		$("#login").html("Login");
		$("#new input:radio").click();
	} else {
		$.ajax({
		type: "POST",
		 url: "login-auth.php",
		 data: {
			 func: "log",
			 email: email
		 },
		 success: function(data) {
			 window.location="./";
		 }
	 });
	}
}
$('#user,#pass').keypress(function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);
 	if(keycode == '13'){
		$("#login").click();
 	}
	event.stopPropagation();
});
$('#password').keypress(function(event){
 var keycode = (event.keyCode ? event.keyCode : event.which);
 if(keycode == '13'){
	 unlock();
 }
 event.stopPropagation();
});
function changeView(sys,name=null) {
	$("#currentSystem").html(name);
	$("#loadView").show();
	$("#updateView").hide();
	if(!sys) {
		sys = "0";
	}
	$.ajax({
	type: "POST",
	 url: "config/function.php",
	 data: {
		 func: "changeView",
		 sys: sys
	 },
	 success: function(data) {
		 $("#updateView").html(data);
		 $("#loadView").hide();
		$("#updateView").show();
	 }
 });
}
function updForm() {
	if($("#staff:checked").val() == "on") {
		$.ajax({
		type: "POST",
		 url: "config/function.php",
		 data: {
			 func: "changelog",
			 sys: 'staff'
		 },
		 success: function(data) {
			 $("#formLog").html(data);
		 }
	 });
	}
	if($("#new:checked").val() == "on") {
		$.ajax({
		type: "POST",
		 url: "config/function.php",
		 data: {
			 func: "changelog",
			 sys: 'new'
		 },
		 success: function(data) {
			 $("#formLog").html(data);
		 }
	 });
	}
}
function changeTime() {
	month = $("#report_month").val();
	year = $("#report_year").val();
	if(year != "Select Year") {
		$("#timeline").html("<center><img src='src/images/spin.gif'></center>");
		$.ajax({
		type: "POST",
		 url: "config/function.php",
		 data: {
			 func: "changeTime",
			 year: year,
			 month: month
		 },
		 success: function(data) {
			 $("#timeline").html(data);
		 }
	 });
	}
}
function updateStatus(task,id) {
  status = document.getElementById(task+"_"+id).checked;
  $.ajax({
  type: "POST",
   url: "config/function.php",
   data: {
     func: "updateStatus",
     id: id,
		 task: task,
     status: status
   },
   success: function(data) {
     success();
   }
 });
}
// function updateStatus(id) {
//   status = document.getElementById("check"+id).checked;
//   $.ajax({
//   type: "POST",
//    url: "config/function.php",
//    data: {
//      func: "updateStatus",
//      id: id,
//      status: status
//    },
//    success: function(data) {
//      success();
//    }
//  });
// }
$('#searchTxt').keypress(function(event){
 var keycode = (event.keyCode ? event.keyCode : event.which);
 keyword = $("#searchTxt").val();
 if(keycode == '13' && keyword != ""){
	 searchDirectory();
 }
 event.stopPropagation();
});
function searchDirectory(admin=null) {
	keyword = $("#searchTxt").val().trim();
	dept = $("#searchSelect").val();
	lvl = $("#levelSelect").val();
	if(!keyword && dept == 0 && lvl == 0) {
		$("#searchTxt").val("");
		fail("Please Enter Staff Name or Select Any Department/Level")
	} else {
		$("#search").prop('disabled', true);
		$("#search").html("<i class='fa fa-gear fa-spin'></i> Loading");
		$("#searchDirectory").html("<center><img src='src/images/spin.gif'></center>");
		$.ajax({
	  type: "POST",
	   url: "config/function.php",
	   data: {
	     func: "searchDirectory",
	     keyword: keyword,
	     dept: dept,
			 lvl: lvl,
			 admin: admin
	   },
	   success: function(data) {
	     $("#searchDirectory").html(data);
			 $("#search").prop('disabled', false);
	 		 $("#search").html("<i class='fa fa-search'></i> Search");
			 var elems = Array.prototype.slice.call(document.querySelectorAll('.switch-btn-modal'));
			 $('.switch-btn-modal').each(function() {
	 			new Switchery($(this)[0], $(this).data());
	 		});
	   }
	 });
 }
}
function showModal(task=null,id=null,title=null) {
	if(task == null) {
		func = "systemDetail";
	} else {
		func = "system"+task;
	}
	if(title == null){
		if(task == "dms"){
			title = "Document Management System";
		} else if(task == "form"){
			title = "New";
		} else {
			title = task;
		}
	}
	modal = "modal-md";
	if(task == "Detail"){
		modal = "modal-lg";
	}
	footer = '<button type="button" id="saved" class="btn btn-primary btn-sm" onclick="saved()"><i class="fa fa-envelope"></i> Save</button>';
	footer += '<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>';
	$("#"+modal+" .modal-title").html(title);
	$("#"+modal+" .modal-footer").html(footer);
	$("#"+modal).modal("show");
	$.ajax({
	type: "POST",
	 url: "config/function.php",
	 data: {
		 func: func,
		 keyword: id
	 },
	 success: function(data) {
		 $("#"+modal+" .modal-body").html(data);
		 if(task == "Detail"){
			 $(".datetimepicker").datepicker({timepicker:!0,language:"en",autoClose:!0,dateFormat:"dd MM yyyy"});
		 }
	 }
 });
}
function adminModal(task=null,id=null) {
	title = "Edit ";
	if(id == null){
		title = "New ";
	}
	if(task == "system"){
		title += "System";
	} else if(task == "phone"){
		title += "Extension";
	} else if(task == "dms"){
		title += "Document Management System";
	} else if(task == "level"){
		title += "Level";
	} else if(task == "department"){
		title += "Department";
	} else if(task == "division"){
		title += "Division";
	} else if(task == "policys"){
		title += "Category";
	} else if(task == "policy"){
		title = "New Category";
	} else if(task == "policyNew"){
		title = "New Sub Category";
	} else if(task == "attachPolicy"){
		title = "New Policy";
	} else if(task == "newCategory"){
		title = "New Sub Category";
	} else if(task == "category"){
		title = "Edit Category";
	} else if(task == "attachmentPolicy"){
		title = "New Policy";
	} else if(task == "attachmentForm"){
		title = "New Form";
	} else if(task == "forms"){
		title = "Edit Category";
	}  else if(task == "form"){
		title = "New Category";
	} else if(task == "attachForm"){
		title = "Edit Form";
	}

	modal = "modal-md";
	if(task == "attach"){
		modal = "modal-lg";
	}
	footer = "<button type=\"button\" id=\"saveAdmin\" class=\"btn btn-success btn-sm\" onclick=\"saveAdmin('"+task+"')\"><i class=\"fa fa-envelope\"></i> Save</button>";
	footer += '<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>';
	$("#"+modal+" .modal-title").html(title);
	$("#"+modal+" .modal-footer").html(footer);
	$("#"+modal).modal("show");
	$.ajax({
	type: "POST",
	 url: "config/function.php",
	 data: {
		 func: "admin",
		 task: task,
		 id: id
	 },
	 success: function(data) {
		 $("#"+modal+" .modal-body").html(data);
	 }
 });
}
function saveAdmin(task){
	submit = true;
	fd = new FormData();
  fd.append('func',"saveAdmin");
	fd.append('task',task);
	fd.append('id',$("#task_id").val());
	if(task == "system"){
		if(!$("#name").val() || !$("#url").val() || !$("#database").val()){
			alert($("#name").val()+$("#url").val()+$("#database").val())
			fail("Please enter all required field");
			submit = false;
		} else {
			fd.append('system',$("#name").val());
			fd.append('url',$("#url").val());
			fd.append('database',$("#database").val());
		}
	} else if(task == "dms"){
		if(!$("#name").val() || !$("#url").val()){
			fail("Please enter all required field");
			submit = false;
		} else {
			fd.append('system',$("#name").val());
			fd.append('url',$("#url").val());
		}
	} else if(task == "phone"){
		if(!$("#name").val() || !$("#ext").val() || $("#department_form").val() == 0 || $("#level_form").val() == 0){
			fail("Please enter all required field");
			submit = false;
		} else {
			fd.append('name',$("#name").val());
			fd.append('ext',$("#ext").val());
			fd.append('hp',$("#hp").val());
			fd.append('dept',$("#department_form").val());
			fd.append('level',$("#level_form").val());
		}
	} else if(task == "level"){
		if(!$("#level_form").val()){
			fail("Please enter all required field");
			submit = false;
		} else {
			fd.append('level',$("#level_form").val());
		}
	} else if(task == "division"){
		if(!$("#division_form").val()){
			fail("Please enter all required field");
			submit = false;
		} else {
			fd.append('division',$("#division_form").val());
		}
	} else if(task == "department"){
		if($("#division_form").val() == 0 || !$("#department_form").val()){
			fail("Please enter all required field");
			submit = false;
		} else {
			fd.append('division',$("#division_form").val());
			fd.append('department',$("#department_form").val());
		}
	} else if(task == "form" || task == "forms"){
		if(!$("#desc").val()){
			submit = false;
			fail("Please enter all required field");
		} else {
			fd.append('desc',$('#desc').val());
		}
	} else if(task == "attachmentForm"){
		if(!$("#desc").val() || !$('#url').val()){
			submit = false;
			fail("Please enter all required field");
		} else {
			fd.append('desc',$('#desc').val());
			fd.append('file',$('#url')[0].files[0]);
		}
	} else if(task == "attachForm"){
		if(!$("#desc").val()){
			submit = false;
			fail("Please enter all required field");
		} else {
			fd.append('desc',$('#desc').val());
			if ($('#url').val() != "") {
				fd.append('file',$('#url')[0].files[0]);
			}
		}
	} else if(task == "attach"){
		if ($('#url').val() != "") {
			switch($('#url').val().substring($('#url').val().lastIndexOf('.') + 1).toLowerCase()){
				case 'pdf':
					fd.append('file',$('#url')[0].files[0]);
					fd.append('desc',$('#desc').val());
				break;
				default:
					fail("Please upload PDF formats");
					submit = false;
				break;
			}
		} else if(!$('#desc').val()){
			submit = false;
			fail("Please enter all required field");
		} else {
			fd.append('desc',$('#desc').val());
		}
	}

	if(submit == true){
		$("#saveAdmin").prop("disabled",true);
		$("#saveAdmin").html("<i class=\"fa fa-spinner fa-spin\"></i> Save</button>");
		$.ajax({
			type: "POST",
			url: "config/function.php",
			data: fd,
			processData: false,
			contentType: false,
			success: function(data) {
				success();
				if(task == "attach" || task == "policys" ||  task == "policyNew" ||  task == "attachPolicy" ||  task == "newCategory" ||  task == "category"){
					task = "policy";
				}
				if(task == "forms" || task == "attachForm)"){
					task = "form";
				}

				$("#"+task).html("<center><img src='src/images/spin.gif'></center>");
				$("#"+task).html(data);
				var elems = Array.prototype.slice.call(document.querySelectorAll('#table_'+task+' .switch-btn'));
				$('#table_'+task+' .switch-btn').each(function() {
					new Switchery($(this)[0], $(this).data());
				});
				$('#table_'+task).DataTable({
					scrollCollapse: true,
					autoWidth: false,
					responsive: true,
					columnDefs: [{
						targets: "datatable-nosort",
						orderable: false,
					}],
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
					"language": {
						"info": "_START_-_END_ of _TOTAL_ entries",
						searchPlaceholder: "Search",
						paginate: {
							next: '<i class="ion-chevron-right"></i>',
							previous: '<i class="ion-chevron-left"></i>'
						}
					},
				});
				$(".modal").modal("hide");
		 }
	 });
	}
}
function saved() {
	task = $("#sys_task").val();
	fd = new FormData();
  fd.append('func',"saved");
	fd.append('task',task);
	if(task == "extension"){
		fd.append('name',$("#name").val());
		fd.append('level',$("#level :selected").val());
		fd.append('extension',$("#extension").val());
		fd.append('mobile',$("#mobile").val());
	} else if(task == "division"){
		fd.append('name',$("#name").val());
	} else if(task == "department"){
		fd.append('name',$("#name").val());
		fd.append('division',$("#div_id :selected").val());
	} else if(task == "level"){
		fd.append('name',$("#name").val());
	} else if(task == "dms"){
		fd.append('name',$("#name").val());
		fd.append('url',$("#url").val());
	} else if(task == "system"){
		alert(task);
		// fd.append('name',$("#name").val());
		// fd.append('url',$("#url").val());
	}
}
function showPDF(title=null,id=null,code=null,type=null,user=null,asset=null) {
	$("#modal-lg").modal("show");
	if(type == "LOAN"){
		title = "Loan IT Equipment Form";
	}
	$("#modal-lg .modal-title").html(title);
	$.ajax({
		type: "POST",
		url: "config/function.php",
		data: {
			func: "pdf",
			id: id,
			code: code,
			type: type,
			user: user,
			asset: asset,
			title: title
		},
		success: function(data) {
			$("#modal-lg .modal-body").html(data);
	 }
 });
}
function checkNotify(temp=0) {
	inter = setTimeout(function(){
		$.ajax({
		type: "POST",
		 url: "config/function.php",
		 data: {
			 func: "checkNotify"
		 },
		 success: function(data) {
			 if(data != 0 && temp != data) {
				 // console.log(data);
				 // const notification = new Notification("New message from MTD Central", {body: "You have "+data+" pending approval"});
				 showNotification();
				 let searchParams = new URLSearchParams(window.location.search);
				 id = searchParams.get('id');
				 if(id == "1"){
					 refreshToken(data);
					 // console.log("Test");
				 }
			 }
			 checkNotify(data);
		 }
	 });
 },30000)
}
function showNotification(){
	$.ajax({
	type: "POST",
	 url: "config/function.php",
	 data: {
		 func: "notify"
	 },
	 success: function(data) {
		 if(data == 0) {
			 $("#showNotify").hide();
		 } else {
			 $("#showNotify").show();
			 $("#showNotification").html(data);
		 }
	 }
 });
}
function refreshToken(data) {
	var sys = ['9','100','110','111'];
	if(sys.indexOf($("#sysID").val()) == -1) {
		if($("#currentSystem").text() == 0 && $('#dash_count').length) {
			d = $('#dash_count').val();
			if(d != data) {
				changeView($("#sysID").val(),$("#currentSystem").text());
			}
		} else {
			changeView($("#sysID").val(),$("#currentSystem").text());
		}
	}
}
// function showNotify(){
// 	$.ajax({
// 	type: "POST",
// 	 url: "config/function.php",
// 	 data: {
// 		 func: "notis"
// 	 },
// 	 success: function(data) {
// 		 if(data == 0) {
// 			 $("#showNotify").hide();
// 		 } else {
// 			 $("#showNotify").show();
// 		 }
// 	 }
//  });
// }
function settimeout(){
	interval = setTimeout(function(){
		getthedate();
	},token)
}
function lockdown() {
	$("#login-modal").modal({
		 backdrop: 'static',
		 keyboard: false
	 });
	$.ajax({
	type: "POST",
	 url: "login-auth.php",
	 data: {
		 func: "lock"
	 },
	 success: function(data) {
	 }
 });
}
function unlock() {
	username = $("#username").val();
	password = $("#getpassword").val();
	$("#log").prop('disabled', true);
	$("#log").html("<i class='fa fa-gear fa-spin'></i> Loading");
	$.ajax({
	type: "POST",
	 url: "login-auth.php",
	 data: {
		 func: "unlock",
		 username: username,
		 password: password
	 },
	 success: function(data) {
		 if(data == "true") {
			 $("#login-modal").modal("hide");
			 $("#password").val("");
		 } else {
			 fail("Invalid Password");
		 }
		 $("#log").prop('disabled', false);
	 	 $("#log").html("Login");
	 }
 });
}
// function saved() {
// 	task = $("#sys_task").val();
// 	if(task == "system") {
// 		id = $("#sys_id").val();
// 		title = $("#sys_title").val();
// 		desc = $("#sys_desc").val();
// 		db = $("#sys_db").val();
// 		url = $("#sys_url").val();
// 		from = null;
// 		to = null;
// 		if(id == 1){
// 			from = $("#sys_from").val();
// 			to = $("#sys_to").val();
// 		}
// 		if(!title || !db || !url) {
// 			fail("All field required");
// 		} else {
// 			$("#saved").disabled = true;
// 			$.ajax({
// 			type: "POST",
// 			 url: "config/function.php",
// 			 data: {
// 				 func: "saveDetail",
// 				 id: id,
// 				 title: title,
// 				 desc: desc,
// 				 db: db,
// 				 url: url,
// 				 from: from,
// 				 to: to
// 			 },
// 			 success: function(data) {
// 				 success();
// 				 // location.reload();
// 			 }
// 		 });
// 		}
// 	} else if(task == "extension"){
// 		id = $("#ext_id").val();
// 	  name = $("#ext_name").val();
// 		code = $("#ext_code").val();
// 		mobile = $("#ext_mobile").val();
// 		level = $("#ext_level").val();
// 		if(!name || !code || !mobile) {
// 			fail("All field required");
// 		} else {
// 			$("#saved").disabled = true;
// 			$.ajax({
// 			type: "POST",
// 			 url: "config/function.php",
// 			 data: {
// 				 func: "saveExtention",
// 				 id: id,
// 				 name: name,
// 				 code: code,
// 				 mobile: mobile,
// 				 level: level
// 			 },
// 			 success: function(data) {
// 				 success();
// 				 // location.reload();
// 			 }
// 		 });
// 		}
// 	}
// }
function openSite(url) {
	window.open(url, "_blank");
}
function updatePage(){
	id = $('#systemID :selected').val();
	for(i = 1; i <= 6; i++) {
	  if(id == i){
			$("#admin"+i).show();
		} else {
			$("#admin"+i).hide();
		}
	}
	// $.ajax({
	// type: "POST",
	//  url: "config/function.php",
	//  data: {
	// 	 func: "admin",
	// 	 id: id
	//  },
	//  success: function(data) {
	// 	 $("#page").html(data);
	//  }
 // });
}
function changeTab(tab) {
	// console.log(tab);
	$("#link_"+tab).removeClass('active');
}
function uploadDoc(){
	fd = new FormData();
  fd.append('func',"uploadDoc");
	if($("#attachment")[0].files[0].size > 5000705){
		fail("File size exceed 5MB");
	} else {
		switch($('#attachment').val().substring($('#attachment').val().lastIndexOf('.') + 1).toLowerCase()){
			case 'pdf':
				fd.append('file',$('#attachment')[0].files[0]);
				if(confirm("Upload Document to Server?")){
					$.ajax({
						type: "POST",
						url: "config/function.php",
						data: fd,
						processData: false,
			      contentType: false,
						success: function(data) {
							$("#page-content").html(data);
					 }
				 });
			 }
			break;
			default:
				fail("Please upload PDF formats");
			break;
		}
	}
}
function uploadImg(){
	fd = new FormData();
  fd.append('func',"uploadImg");
	if($("#attachment")[0].files[0].size > 5000705){
		fail("File size exceed 5MB");
	} else {
		switch($('#attachment').val().substring($('#attachment').val().lastIndexOf('.') + 1).toLowerCase()){
		case 'png': case 'jpg':
			fd.append('file',$('#attachment')[0].files[0]);
				$.ajax({
					type: "POST",
					url: "config/function.php",
					data: fd,
					processData: false,
		      contentType: false,
					success: function(data) {
						if(data && data !=""){
							location.reload();
						}
				 }
			 });
			break;
			default:
				fail("Please upload PNG or JPG formats");
			break;
		}
	}
}
function deleteDoc(id){
	if(confirm("Delete file?")){
		$.ajax({
		type: "POST",
		 url: "config/function.php",
		 data: {
			 func: "deleteDoc",
			 id: id
		 },
		 success: function(data) {
			 $("#page-content").html(data);
		 }
	 });
	}
}
function getDepartment(){
	div = $("#getDiv option:selected").val();
	if(div != 0){
		$.ajax({
		type: "POST",
		 url: "config/function.php",
		 data: {
			 func: "department",
			 id: div
		 },
		 success: function(data) {
			 $("#div").html(data);
			 var elems = Array.prototype.slice.call(document.querySelectorAll('.switch-btn-modal'));
			 $('.switch-btn-modal').each(function() {
	 			new Switchery($(this)[0], $(this).data());
	 		});
		 }
	 });
	}
}
function fail(msg) {
	if(!msg) {
		msg = "Please contact system admin";
	}
	swal({
		type: 'error',
		text: msg,
		showConfirmButton: false,
		timer: 1500
		}
	);
}
function success(msg) {
	if(!msg) {
		msg = "Saved";
	}
	swal({
		type: 'success',
		text: msg,
		showConfirmButton: false,
		timer: 1500
		}
	);
}
