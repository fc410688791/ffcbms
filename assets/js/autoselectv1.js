

/**
 * [Autoselectv1 description]
 *
 * @author leeprince
 * @param {[type]} config [description]
 */
function Autoselectv1(config)
{
	var url = config.requestUrl;
	var selectData = config.selectData;

	var elemString = '';

	for (var i=0; i <= (selectData.length)-1; i++) {
		elem = selectData[i].elem;
		elemString += '#'+elem+',';
	}
	elemString = elemString.substr(0, elemString.length-1);
	execRequest(elemString, url, selectData);

}

/**
 * [execRequest description]
 *
 * @author leeprince
 * @param {[type]} config [description]
 */
function execRequest(elemString, url, selectData) {
	$(elemString).on("change", function(){
		var eSelfThis = $(this);

		var eid = eSelfThis.prop('id');
		var eval = eSelfThis.val();

		var isMatchElem = false;
		for (var i = 0; i <= (selectData.length) - 1; i++) {
			var elemDefaultVal = selectData[i].elemForDefaultVal;
			var elem           = selectData[i].elem;
			var elemFor        = selectData[i].elemFor;
			var method         = selectData[i].method;

			var elemHtml = $('#'+elem);
			var elemForHtml = $('#'+elemFor);
			var html = "<option value=0>"+elemDefaultVal+"</option>";

			if (eid == elem && method != '' && ! isMatchElem) {
				isMatchElem = true;

				elemForHtml.html(html);
				if (eval != 0) {
					var eHtml = html;
					var sendData = setAjaxData(eval, method, selectData[i]);
				
					eAjax(url, sendData, function(res, callBackData) {
						var eHtml = callBackData.eHtml;
						var elemFor = callBackData.elemFor;
						var elemForHtml = callBackData.elemForHtml;

						if (res.code == 0) {
							for (var j=0; j <= (res.data.length) - 1; j++) {
								eHtml += optionSwitch(elemFor, res.data[j]);
							}
							elemForHtml.html(eHtml);
						} else {
			   				toastr.error(res.msg);
						}
						
					}, {eHtml:eHtml,elemForHtml:elemForHtml,elemFor:elemFor});
				}
			} else if (isMatchElem) {
				elemForHtml.html(html);
			} else {

			}
		}
	})
}

/**
 * [setAjaxData description]
 *
 * @author leeprince
 * @param {[type]} elemVal   [description]
 * @param {[type]} method    [description]
 * @param {[type]} extraElem [description]
 */
function setAjaxData(elemVal, method, toSelectData)
{
	var data = {};
	data = {key:elemVal, method:method};

	if (toSelectData.extraElem != undefined && toSelectData.extraElem != '') {
		var extraElemAll = toSelectData.extraElem
		for (var i = 0; i <= (extraElemAll.length) -1; i++) {
			var extraElem = extraElemAll[i];
			var extraElemVal = $('#'+extraElem).val();
			data[extraElem] = extraElemVal;
		}
	}
	return data;
}

/**
 * [optionSwitch description]
 *
 * @author leeprince
 * @param  {[type]} elemFor [description]
 * @param  {[type]} res     [description]
 * @return {[type]}         [description]
 */
function optionSwitch(elemFor, res)
{
	switch (elemFor) {
		case 'city':
			return "<option value="+res.id+">"+res.name+"</option>";
			break;
		case 'street':
			return "<option value="+res.id+">"+res.name+"</option>";
			break;
		case 'village':
			return "<option value="+res.id+">"+res.name+"</option>";
			break;
		default:
			toastr.error("未知错误-optionSwitch");
			return false;
			break;

	}
}



