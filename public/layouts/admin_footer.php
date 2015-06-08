</div>

	<div id="footer">
		Copyright <?php echo date("Y", time()); ?>, Thomas Wood
	</div>

	<?php if(isset($db)) { $db->close_connection(); } ?>

</body>
</html>