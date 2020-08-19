
/**
 * [eAjax description]
 *
 * @author leeprince
 * @param  {[type]}  $url     [description]
 * @param  {[type]}  $data    [description]
 * @param  {String}  type     [description]
 * @param  {String}  dataType [description]
 * @param  {Boolean} async    [description]
 * @return {[type]}           [description]
 */
function eAjax(url, data, callBackFunc, callBackData = {}, type = 'POST', dataType = 'json', async = false)
{
	$.ajax({
		url:url,
		data:data,
		type:type,
		dataType:dataType,
		async:async,
		success:function(res){
			callBackFunc(res, callBackData);
		},
		error: function(xhr, type){ 
		   toastr.error("未知错误");
		}
	})
}

/** 切分字符串为数组 */
function getSplit(str, separator) {
  var newStr = new Array // var newStr = []
  newStr = str.split(separator)
  return newStr
}