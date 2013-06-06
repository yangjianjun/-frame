<script>
$.include([
	'<?php echo $this->baseUrl;?>/js/user.js',
	'<?php echo $this->baseUrl;?>/css/user.css'
]);
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