if ($.fn.pagination){
	$.fn.pagination.defaults.beforePageText = 'Página';
	$.fn.pagination.defaults.afterPageText = 'de {página}';
	$.fn.pagination.defaults.displayMsg = 'Exibindo {from} para {to} de {total} itens';
}
if ($.fn.datagrid){
	$.fn.datagrid.defaults.loadMsg = 'Processamento, aguarde por favor ...';
}
if ($.fn.treegrid && $.fn.datagrid){
	$.fn.treegrid.defaults.loadMsg = $.fn.datagrid.defaults.loadMsg;
}
if ($.messager){
	$.messager.defaults.ok = 'Ok';
	$.messager.defaults.cancel = 'Cancelar';
}
if ($.fn.validatebox){
	$.fn.validatebox.defaults.missingMessage = 'Este campo é obrigatório.';
	$.fn.validatebox.defaults.rules.email.message = 'Digite um endereço de e-mail válido.';
	$.fn.validatebox.defaults.rules.url.message = 'Por favor, digite uma URL válida.';
	$.fn.validatebox.defaults.rules.length.message = 'Por favor insira um valor entre {0} e {1}.';
	$.fn.validatebox.defaults.rules.remote.message = 'Corrija este campo.';
}
if ($.fn.numberbox){
	$.fn.numberbox.defaults.missingMessage = 'Este campo é obrigatório.';
}
if ($.fn.combobox){
	$.fn.combobox.defaults.missingMessage = 'Este campo é obrigatório.';
}
if ($.fn.combotree){
	$.fn.combotree.defaults.missingMessage = 'Este campo é obrigatório.';
}
if ($.fn.combogrid){
	$.fn.combogrid.defaults.missingMessage = 'Este campo é obrigatório.';
}
if ($.fn.calendar){
	$.fn.calendar.defaults.weeks = ['D','S','T','Q','Q','S','S'];
	$.fn.calendar.defaults.months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
}
if ($.fn.datebox){
	$.fn.datebox.defaults.currentText = 'Hoje';
	$.fn.datebox.defaults.closeText = 'Fechar';
	$.fn.datebox.defaults.okText = 'Ok';
	$.fn.datebox.defaults.missingMessage = 'Este campo é obrigatório.';
}
if ($.fn.datetimebox && $.fn.datebox){
	$.extend($.fn.datetimebox.defaults,{
		currentText: $.fn.datebox.defaults.currentText,
		closeText: $.fn.datebox.defaults.closeText,
		okText: $.fn.datebox.defaults.okText,
		missingMessage: $.fn.datebox.defaults.missingMessage
	});
}
