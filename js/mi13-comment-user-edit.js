function mi13commentedit(val, id, commenttext) {
	let comment = document.getElementById('comment') || 0;
	let edit = document.getElementById('mi13_comment_user_edit_id') || 0;
	let sbm = document.getElementById('submit') || 0;
	if(('value' in comment) && ('value' in edit) && ('value' in sbm)) {
		comment.value = unescape(commenttext);
		edit.value = id;
		sbm.value = val;
		comment.focus();
	}
	return false;
}