function renderdownload(value){
	var check="";
	if(value['level']==1){
		check="checked=checked"
	}
	var content="<tr class='template-download fade in'><td class='preview'><a href="+value['url']+" title="+value['nameimg']+" rel='gallery' download="+value['nameimg']+"><img src="+value['url']+"></a></td><td class='name'><a href="+value['url']+" title="+value['nameimg']+" rel='gallery' download="+value['nameimg']+">"+value['name']+"</a></td><td class='size'><span>"+value['size']+"</span></td><td colspan='2'></td><td class='delete'><button class='btn btn-danger' data-type='POST' data-url="+value['del_url']+"><i class='icon-trash icon-white'></i><span>Delete</span></button><input type='checkbox' name='delete' value='1'></td><td class='delete'><span>Primary</span><input onclick='setprimary(this)' type='radio' name='primary' class='primary' "+check+" value='"+value['id']+"'></td></tr>";
	return content;
}