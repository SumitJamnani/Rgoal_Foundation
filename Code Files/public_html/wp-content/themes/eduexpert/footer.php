</div><!-- /.row -->
</div><!-- /.container -->
</div><!-- /.content-wrapper -->
</div><!-- /.page-wrap -->
	
	<?php
		get_template_part('template-files/footer', 'widget');
	?>
    <footer class="site-footer">
		<div class="site-copyright container">
			<?php if (function_exists('site_copyright')){
				site_copyright();
			}else{?>
				
				<center><?php printf(__('%1$s', 'eduexpert'), '• Copyright © <script>document.write(new Date().getFullYear())</script> <a href="http://www.rgoal.in/" rel="designer">@RGOAL FOUNDATION</a> All Rights Reserved | Developed By :- <a href="https://tinyurl.com/sumitjamnani" rel="designer">Sumit Jamnani</a> | <a href="http://www.rgoal.in/privacy-policy/" >PrivacyPolicy</a> •'); ?></span>
			<?php }?></center>
			<div class="to-top">
				<i class="fa fa-angle-up"></i>
			</div>
		</div>
    </footer><!-- /.site-footer -->
	
	<?php wp_footer(); ?> 
  </body>
</html>