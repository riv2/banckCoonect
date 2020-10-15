function getUniq(trackerHash) {
	
var uniqueHash = jQuery.cookie(trackerHash);
		if (uniqueHash == null) {
		unique = 'uniq';
		uniqHash1 = Math.floor(Math.random() * (9999999 - 1 + 1)) + 1;
		uniqHash2 = Math.floor(Math.random() * (9999999 - 1 + 1)) + 1;
		uniqHash3 = Math.floor(Math.random() * (9999999 - 1 + 1)) + 1;
		uniqueHash = uniqHash1 +'a' + uniqHash2 + 'b' + uniqHash3;
		jQuery.cookie(trackerHash, uniqueHash);
		} else {
		unique = 'notUniq';
		}
	var args = {uniq:unique, uniqHash:uniqueHash};
	return args;
	
}

function uniqClick(trackerHash,trackerRule,ftype) {
	var uniqueClick = jQuery.cookie(trackerHash+'_'+trackerRule+'_'+ftype);
	if (uniqueClick == null) {
	unique = 'u';
	jQuery.cookie(trackerHash+'_'+trackerRule+'_'+ftype, 'DFGdfg34t34tgg3egDFG44dfeg');} else {
	unique = '';	
	}
	return unique;
}