<script>
(function($){
	
	var setJs=function (js){
		alert(js);
		if(!js){
			return false ;
		}
		var script = $("<script />");
		alert(script);
	}
	
	setJs("<?php echo $this->baseUrl;?>/js/common.js");
})(jQuery);
</script>

<table>
<?php 
if (count($this->data)>0):
foreach ($this->data as $v) {
	echo "<tr>
			<td>{$v['name']}</td><td>{$v['passwd']}</td>
		  </tr>";
}
endif;
?>

</table>