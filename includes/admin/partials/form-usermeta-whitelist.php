<script type="text/javascript">

	//「ADD」を押したら増やす
	jQuery(function() {
		jQuery(document).on("click",'#btn_add_textbox', function(){
			jQuery('#input-text-list').append( '<li class="whitelist-item"><label><input type="textbox" class="form-control" name="whitelist[]"><button type="button" id="btn_remove_textbox" class="button btn-primary remove">delete</button></label></li>');
		});
		//「REMOVE」を押したら減らす
		jQuery(document).on('click','.remove', function(){
			jQuery(this).closest('.whitelist-item').remove();
		});
	});

</script>

<button type="button" id="btn_add_textbox" class="button btn btn-primary">add textbox</button>

<div>
	<ul id="input-text-list">
		<!-- //TODO populate from setings -->
		<li class="whitelist-item">
			<label>
				<input type="textbox" class="form-control" name="whitelist[]">
				<button type="button" id="btn_remove_textbox" class="button btn-primary remove">delete</button>
			</label>
		</li>
	</ul>
</div>
