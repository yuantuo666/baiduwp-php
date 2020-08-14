$(".fa-file").each(function() {
	var icon = getIconClass($(this).next().text());
	if (icon !== "") {
		if ($.inArray(icon, ['fa-windows', 'fa-android', 'fa-apple']) >= 0) $(this).removeClass("far").addClass("fab");
		$(this).removeClass("fa-file").addClass(icon);
	}
});