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