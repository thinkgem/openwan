<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html>
<head>
<meta name="GENERATOR" content="qeephp-build-tools http://qeephp.com">
</head>
<body>
<object type="text/site properties">
<param name="Window Styles" value="0x800025">
<param name="FrameName" value="right">
<param name="ImageType" value="Folder">
<param name="comment" value="title:Online Help">
<param name="comment" value="base:index.html">
</object>

<ul>
	<li><object type="text/sitemap">
		<param name="Name" value="<?php echo h($book->subject); ?>">
		<param name="Local" value="index.html">
		</object>
      <?php foreach ($book->chapters as $chapter): ?>
	<li><object type="text/sitemap">
		<param name="Name" value="<?php echo h($chapter->subject); ?>">
		<param name="Local" value="node-<?php echo h($chapter->filename); ?>.html">
		</object>
	<ul>
    <?php foreach ($chapter->sections as $section): ?>
		<li><object type="text/sitemap">
            <param name="Name" value="<?php echo h($section->subject); ?>">
            <param name="Local" value="node-<?php echo h($section->filename); ?>.html">
			</object>
	<?php endforeach; ?>
	</ul>
<?php endforeach; ?>
</ul>

</body>
</html>

